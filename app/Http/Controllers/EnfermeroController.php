<?php

namespace App\Http\Controllers;

use App\Models\Enfermero;
use App\Models\Estado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\RutChileno;
use App\Helpers\RutHelper;

class EnfermeroController extends Controller
{
    
    public function index()
    {
        $enfermeros = Enfermero::with('user')->get();
        return view('admin.enfermeros.index', compact('enfermeros'));
    }

   
    public function create()
    {
        //Retorna la vista para crear una nuevo enfermero
        return view('admin.enfermeros.create');
    }

    
    public function store(Request $request)
    {
        $rutNormalizado = RutHelper::normalizar($request->rut);
        $request->merge(['rut' => $rutNormalizado]);
        
        $request->validate([
            'nombre' => 'required|max:50',
            'apellido' => 'required|max:50',
            'rut' => ['required', 'max:10', 'unique:users,rut', new RutChileno],
            'password' => 'required|min:8|confirmed',
        ]);     
        
        $usuario = new User();
        $usuario->name = strtoupper($request->nombre);
        $usuario->apellido = strtoupper($request->apellido);
        $usuario->rut = $request->rut;
        $usuario->password = Hash::make($request->password);
        $usuario->save();     
        
        $enfermero = new Enfermero();
        $enfermero->user_id = $usuario->id;
        $enfermero->nombre = strtoupper($request->nombre);
        $enfermero->apellido = strtoupper($request->apellido);
        $enfermero->rut = $rutNormalizado;
        $enfermero->save();

        $usuario->assignRole('enfermero');

        return redirect()->route('admin.enfermeros.index')
            ->with('mensaje', 'Enfermero creado exitosamente.')
            ->with('icono','success');
    }

    
    public function show($id)
    {
        //Retorna la vista para mostrar los detalles de un enfermero
        //FindOrFail busca el enfermero por ID y lanza una excepción si no se encuentra
        $enfermero = Enfermero::with('user')->findOrFail($id);
        return view('admin.enfermeros.show', compact('enfermero')); 
    }

    
    public function edit($id)
    {
        $enfermero = Enfermero::with('user')->findOrFail($id);
        return view('admin.enfermeros.edit', compact('enfermero')); 
    }
    
    
    public function update(Request $request, $id)
    {
        // Normalizar el RUT antes de validar
        $request->merge([
            'rut' => RutHelper::normalizar($request->rut),
        ]);
    
        // Buscar el perfil del enfermero
        $enfermero = Enfermero::findOrFail($id);
    
        // Buscar la cuenta de usuario vinculada mediante user_id
        $usuario = User::findOrFail($enfermero->user_id);
    
        // Validar los datos
        $request->validate([
            'nombre' => 'required|max:50',
            'apellido' => 'required|max:50',
            'rut' => [
                'required',
                'max:10',
                'unique:enfermeros,rut,' . $enfermero->id,
                'unique:users,rut,' . $usuario->id,
                new RutChileno,
            ],
            'password' => 'nullable|min:8|confirmed',
        ]);
    
        // Actualizar datos del enfermero
        $enfermero->nombre = strtoupper($request->nombre);
        $enfermero->apellido = strtoupper($request->apellido);
        $enfermero->rut = $request->rut;
        $enfermero->save();
    
        // Actualizar datos del usuario relacionado
        $usuario->name = strtoupper($request->nombre);
        $usuario->apellido = strtoupper($request->apellido);
        $usuario->rut = $request->rut;
    
        // Actualizar la contraseña sólo si se ingresó una nueva
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
    
        $usuario->save();
    
        return redirect()
            ->route('admin.enfermeros.index')
            ->with('mensaje', 'Datos actualizados exitosamente.')
            ->with('icono', 'success');
    }
    public function destroy($id)
    {
        //Elimina el enfermero y el usuario asociado
        $enfermero = Enfermero::find($id);
        //Eliminar el usuario asociado, estamos trayendo del modelo enfermero hacia el usuario
        $user = $enfermero->user;
        $user->delete();
        
        //Eliminar el enfermero
        $enfermero->delete();

        return redirect()->route('admin.enfermeros.index')
            ->with('mensaje', 'Enfermero eliminado exitosamente.')
            ->with('icono','success');
    }
}
