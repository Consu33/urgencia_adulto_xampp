<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Atencion;
use App\Models\Paciente;
use App\Models\Categoria;
use App\Models\Estado;
use Illuminate\Support\Facades\Cache;
use App\Rules\RutChileno;

class AtencionController extends Controller
{
    public function create($paciente_id)
    {
        $paciente = Paciente::findOrFail($paciente_id);
        $categorias = Categoria::all();
        $estados = Estado::all();

        return view('admin.atenciones.create', compact('paciente', 'categorias', 'estados'));
    }

    public function store(Request $request)
    {
        // Buscar paciente existente por rut
        $pacienteExistente = Paciente::where('rut', $request->input('rut'))->first();

        // Si el paciente existe y está inactivo, reactivarlo y registrar nueva atención
        if ($pacienteExistente) {
            $estadoInicial = Estado::where('nombre', 'ingresado')->first();
            $categoriaSinCategorizar = Categoria::where('codigo', 'SIN CATEGORIZAR')->first();

            // Reactivar paciente si está inactivo
            if (!$pacienteExistente->activo) {
                $pacienteExistente->activo = true;
                $pacienteExistente->estado_id = $estadoInicial?->id;
                $pacienteExistente->categoria_id = $categoriaSinCategorizar?->id; // Aseguramos categoría inicial
                $pacienteExistente->save();
            }

            // Registrar nueva atención
            $atencion = Atencion::create([
                'paciente_id' => $pacienteExistente->id,
                'estado_id' => $estadoInicial?->id,
                'categoria_id' => $categoriaSinCategorizar?->id,
                'fecha_atencion' => now(),
                'observaciones' => 'Atención automática al reingresar paciente',
            ]);

            // Guardar en cache la última atención sin categorizar para notificar a la vista condition
            if ($categoriaSinCategorizar) {
                Cache::put('ultima_atencion_sin_categorizar', [
                    'atencion_id' => $atencion->id,
                    'paciente_id' => $pacienteExistente->id,
                    'nombre' => $pacienteExistente->nombre,
                    'apellido' => $pacienteExistente->apellido,
                    'rut' => $pacienteExistente->rut,
                    'fecha' => $atencion->fecha_atencion->toDateTimeString(),
                ], now()->addMinutes(60));
            }

            //  Recargar paciente con atención recién creada
            $pacienteExistente->load([
                'atenciones' => fn($q) => $q->orderByDesc('fecha_atencion'),
                'atenciones.categoria',
                'estado'
            ]);

            // Guardamos las flags en flash para que estén disponibles en la siguiente petición
            session()->flash('paciente_existente', $pacienteExistente);
            session()->flash('paciente_nuevo_id', $pacienteExistente->id);
            session()->flash('paciente_reactivado', true);

            return redirect()->back()
                ->with('mensaje', 'Paciente reactivado y atención registrada correctamente.')
                ->with('icono', 'success');
        }

        // Si no existe, validar y crear nuevo paciente
        $tipo = $request->input('identificacion_tipo');
        // Reglas de validación
        $rules = [
            'nombre' => 'required|max:50',
            'apellido' => 'required|max:50',
            'identificacion_tipo' => 'required|in:rut,pasaporte,ficha',
            'rut' => 'required|max:12|unique:pacientes',
        ];
        // Agregar regla personalizada para RUT chileno
        if ($tipo === 'rut') {
            $rules['rut'] = new RutChileno;
        }
        // Validar datos
        $request->validate($rules);
        // Crear nuevo paciente
        $estadoInicial = Estado::where('nombre', 'ingresado')->first();
        //  Crear paciente
        $paciente = new Paciente();
        $paciente->nombre = $request->nombre;
        $paciente->apellido = $request->apellido;
        $paciente->rut = $request->rut;
        $paciente->identificacion_tipo = $tipo;
        $paciente->estado_id = $estadoInicial?->id;
        $paciente->save();

        // Registrar atención inicial
        $atencion = Atencion::create([
            'paciente_id' => $paciente->id,
            'estado_id' => $estadoInicial?->id,
            'categoria_id' => Categoria::where('codigo', 'SIN CATEGORIZAR')->value('id'),
            'fecha_atencion' => now(),
            'observaciones' => 'Atención inicial al registrar paciente',
        ]);

        // Guardar en cache para notificar a la vista condition
        $categoriaSinId = Categoria::where('codigo', 'SIN CATEGORIZAR')->value('id');
        if ($atencion && $atencion->categoria_id == $categoriaSinId) {
            Cache::put('ultima_atencion_sin_categorizar', [
                'atencion_id' => $atencion->id,
                'paciente_id' => $paciente->id,
                'nombre' => $paciente->nombre,
                'apellido' => $paciente->apellido,
                'rut' => $paciente->rut,
                'fecha' => $atencion->fecha_atencion->toDateTimeString(),
            ], now()->addMinutes(60));
        }

        // Recargar el paciente recién creado
        $paciente->load([
            'atenciones' => fn($q) => $q->orderByDesc('fecha_atencion'),
            'atenciones.categoria',
            'estado'
        ]);

        // Guardamos en flash el paciente creado para la siguiente petición
        session()->flash('paciente_existente', $paciente);
        session()->flash('paciente_nuevo_id', $paciente->id);

        return redirect()->back()
            ->with('mensaje', 'Paciente registrado exitosamente con atención inicial.')
            ->with('icono', 'success');
    }
}
