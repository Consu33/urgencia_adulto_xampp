<?php

namespace App\Http\Controllers;

use App\Events\PatientStatusUpdated;
use App\Models\Paciente;
use App\Models\Categoria;
use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoPacienteController extends Controller
{
    public function index()
    {
        $panel = $this->calcularPanel();
        return view('admin.panel', $panel);
    }

    //función para mostrar el panel en la TV
    public function tv()
    {
        $panel = $this->calcularPanel(); 

        return view('admin.panel_urgencia', [
            'hayCriticos' => $panel['hayCriticos'],
            'categorias' => $panel['categorias'],
        ]);
    }

    public function panelUrgenciaDinamico()
    {
        $panel = $this->calcularPanel();

        return view('admin.panel_urgencia_parcial', [
            'hayCriticos' => $panel['hayCriticos'],
            'categorias' => $panel['categorias'],
        ]);
    }

    public function getPacienteJson($id)
    {
        $paciente = Paciente::with('estado', 'categoria')->findOrFail($id);

        return response()->json([
            'id' => $paciente->id,
            'nombre' => $paciente->nombre,
            'apellido' => $paciente->apellido,
            'rut' => $paciente->rut,
            'estado_id' => $paciente->estado_id,
            'estado_nombre' => $paciente->estado->nombre,
            'categoria_id' => $paciente->categoria_id,
            'categoria_nombre' => optional($paciente->categoria)->nombre,
            'ultima_categoria' => optional($paciente->categoria)->nombre,
            'total_atenciones' => $paciente->atenciones()->count(),
        ]);
    }

    public function condition()
    {
        //carga las relaciones necesarias para mostrar en la vista condition
        $pacientes = Paciente::with(['categoria', 'estado'])
            // filtra los pacientes activos = 1
            ->where('activo', true)
            //ejecuta la consulta y obtiene los resultados
            ->get();
        $categorias = Categoria::all();
        $estados = Estado::all();

        $this->authorize('admin.pacientes.condition');
        return view('admin.condition', compact('pacientes', 'categorias', 'estados'));
    }

    public function updateCategory(Request $request, $id)
    {
        // Validar entrada
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'estado_id' => 'required|exists:estados,id',
        ]);

        // Buscar paciente
        $paciente = Paciente::findOrFail($id);

        // Guardar estado anterior (opcional para auditoría)
        $estadoAnterior = optional($paciente->estado)->nombre;

        // Actualizar categoría y estado
        $paciente->categoria_id = $validated['categoria_id'];
        $paciente->estado_id = $validated['estado_id'];
        $paciente->save();

        // Recargar relaciones actualizadas
        $paciente->load(['categoria', 'estado']);

        // Obtener código de categoría y nombre de estado
        $codigoCategoria = optional($paciente->categoria)->codigo;
        $nombreEstado = optional($paciente->estado)->nombre;

        // Definir umbrales base
        $umbralesBase = [
            'ESI 1' => 0,
            'ESI 2' => 15,
            'ESI 3' => 30,
            'ESI 4' => 45,
            'ESI 5' => 60,
            'ESPERA-CAMA' => 60,
        ];

        // Verificar si hay pacientes críticos (ESI 1)
        $hayCriticos = Paciente::with('categoria')
            ->whereHas('estado', fn($q) => $q->where('nombre', '!=', 'Dado de Alta'))
            ->get()
            ->where('categoria.codigo', 'ESI 1')
            ->count() > 0;

        // Ajustar umbral si hay críticos
        $umbralBase = $umbralesBase[$codigoCategoria] ?? 0;
        $umbralAjustado = $hayCriticos ? $umbralBase + 60 : $umbralBase;

        // Calcular cantidad de pacientes en misma categoría y estado (excluyendo al actual si ya fue atendido)
        $cantidadEnEspera = Paciente::where('categoria_id', $validated['categoria_id'])
            ->whereHas('estado', fn($q) => $q->where('nombre', $nombreEstado))
            ->where('id', '!=', $paciente->id) // excluye al paciente actual si ya fue actualizado
            ->count() + 1; // incluye al paciente actual como el último en la fila

        // Calcular tiempo estimado dinámico
        $tiempoEstimado = $nombreEstado === 'En espera de atencion'
            ? $cantidadEnEspera * $umbralAjustado
            : 0;

        // Devolver respuesta JSON con datos útiles
        return response()->json([
            'success' => true,
            'categoria' => $codigoCategoria,
            'estado' => $nombreEstado,
            'cantidadEnEstado' => $cantidadEnEspera,
            'tiempoEstimado' => $tiempoEstimado,
            'umbralBase' => $umbralBase,
            'umbralAjustado' => $umbralAjustado,
        ]);
    }

    function mapColor($color)
    {
        $validColors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

        return in_array($color, $validColors) ? $color : ($color === 'orange' ? 'orange' : 'secondary');
    }

    public function panelDinamico()
    {
        $panel = $this->calcularPanel();
        return view('admin.panel.parcial', $panel);
    }

    private function calcularPanel()
    {
        $categorias = Categoria::all();

        $estadosClave = [
            'En espera de atencion',
            'En atencion',
        ];

        $cupos = [
            'ESI 1' => 2,
            'ESI 2' => 8,
            'ESI 3' => 50,
            'ESI 4' => 7,
            'ESI 5' => 4,
            'ESPERA-CAMA' => 35,
        ];

        $umbralesBase = [
            'ESI 1' => 0,
            'ESI 2' => 15,
            'ESI 3' => 30,
            'ESI 4' => 45,
            'ESI 5' => 60,
            'ESPERA-CAMA' => 60,
        ];

        $iconos = [
            'En espera de atencion' => 'bi bi-hourglass-split',
            'En atencion' => 'bi bi-heart-pulse-fill',
            'En espera de cama' => 'fas fa-bed',
        ];

        // Obtener todos los pacientes activos (excluye "Dado de Alta")
        $pacientes = Paciente::with(['categoria', 'estado'])
            ->whereHas('estado', fn($q) => $q->where('nombre', '!=', 'Dado de Alta'))
            ->get();

        // Pacientes ESI 1 que están en espera o en atención (impactan y activan alerta)
        $esi1Impactantes = $pacientes->filter(function ($p) {
            $estado = optional($p->estado)->nombre;
            $codigo = optional($p->categoria)->codigo;
            return $codigo === 'ESI 1' && in_array($estado, ['En espera de atencion', 'En atencion']);
        });

        $hayCriticos = $esi1Impactantes->count() > 0;
        $impactoESI1 = $esi1Impactantes->count() * 60;

        // Pacientes en espera de atención (para conteo por categoría)
        $pacientesEnEspera = $pacientes->filter(fn($p) => optional($p->estado)->nombre === 'En espera de atencion');

        $conteoPorCategoria = $pacientesEnEspera->groupBy(fn($p) => optional($p->categoria)->codigo)
            ->map(fn($grupo) => $grupo->count());

        $data = [];

        foreach ($categorias as $categoria) {
            if ($categoria->codigo === 'ESI 6') continue;

            $categoriaPacientes = $pacientes->where('categoria_id', $categoria->id);
            $totalPacientes = $categoriaPacientes->count();

            $estados = collect($estadosClave)->mapWithKeys(function ($estadoNombre) use ($categoriaPacientes, $iconos) {
                $estadoPacientes = $categoriaPacientes->where('estado.nombre', $estadoNombre);

                return [
                    $estadoNombre => [
                        'cantidad' => $estadoPacientes->count(),
                        'promedio' => 0,
                        'icono' => $iconos[$estadoNombre] ?? 'fas fa-question-circle',
                    ],
                ];
            })->toArray();

            // Tiempo propio por pacientes en espera de atención en su categoría
            $tiempoPropio = ($conteoPorCategoria[$categoria->codigo] ?? 0) * ($umbralesBase[$categoria->codigo] ?? 0);

            // Impacto de ESI 1 (solo si categoría no es ESPERA-CAMA)
            $impactoESI1PorCategoria = $categoria->codigo !== 'ESPERA-CAMA' ? $impactoESI1 : 0;

            // Impacto cruzado si categoría es ESI 4 o ESI 5
            /*$impactoCruzado = 0;
            if (in_array($categoria->codigo, ['ESI 4', 'ESI 5'])) {
                $impactoCruzado += ($conteoPorCategoria['ESI 2'] ?? 0) * ($umbralesBase['ESI 2'] ?? 0);
                $impactoCruzado += ($conteoPorCategoria['ESI 3'] ?? 0) * ($umbralesBase['ESI 3'] ?? 0);
            }*/

            $tiempoTotal = $tiempoPropio + $impactoESI1PorCategoria;

            if (isset($estados['En espera de atencion'])) {
                $estados['En espera de atencion']['promedio'] = $categoria->codigo === 'ESI 1' ? 0 : $tiempoTotal;
            }

            $data[] = [
                'codigo' => $categoria->codigo,
                'nombre' => $categoria->nombre,
                'color' => str_replace('bg-', '', $categoria->color),
                'cupo' => $cupos[$categoria->codigo] ?? 0,
                'total' => $totalPacientes,
                'umbrales' => $umbralesBase[$categoria->codigo] ?? 30,
                'estados' => $estados,
            ];
        }

        // Procesar ESPERA-CAMA (sin impacto cruzado ni ESI 1)
        $esperaCamaPacientes = $pacientes->where('estado.nombre', 'En espera de cama');
        $cantidadCama = $esperaCamaPacientes->count();
        $tiempoEstimadoCama = $cantidadCama * ($umbralesBase['ESPERA-CAMA'] ?? 60);

        $data[] = [
            'codigo' => 'ESPERA-CAMA',
            'nombre' => 'Pacientes en espera de cama',
            'color' => 'secondary',
            'icono' => 'fas fa-procedures',
            'cupo' => $cupos['ESPERA-CAMA'],
            'total' => $cantidadCama,
            'umbrales' => $umbralesBase['ESPERA-CAMA'],
            'estados' => [
                'En espera de cama' => [
                    'cantidad' => $cantidadCama,
                    'promedio' => $tiempoEstimadoCama,
                    'icono' => 'fas fa-procedures',
                ]
            ]
        ];

        return ['categorias' => $data, 'hayCriticos' => $hayCriticos];
    }
}
