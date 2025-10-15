<?php

namespace App\Http\Controllers;

use App\Models\AdminEnfermero;
use App\Models\Estado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\RutChileno;
use App\Helpers\RutHelper;

class AdminEnfermeroController extends Controller
{
    
    public function index()
    {
        $adminEnfermeros = AdminEnfermero::with('user')->get();
        return view('admin.admin_enfermeros.index', compact('adminEnfermeros'));
    }
    
    public function create()
    {
        return view('admin.admin_enfermeros.create');
    }

    
    public function store(Request $request)
    {
        $request->merge(['rut' => RutHelper::normalizar($request->rut)]);
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

        $adminEnfermero = new AdminEnfermero();
        $adminEnfermero->user_id = $usuario->id; 
        $adminEnfermero->nombre = strtoupper($request->nombre);
        $adminEnfermero->apellido = strtoupper($request->apellido);
        $adminEnfermero->rut = $request->rut;
        $adminEnfermero->save();

        $usuario->assignRole('administrador_enfermero');

        return redirect()->route('admin.admin_enfermeros.index')
            ->with('mensaje','Registro Exitoso!')
            ->with('icono','success'); 
    }

    
    public function show($id)
    {
        $adminEnfermero = AdminEnfermero::with('user')->findOrFail($id);
        return view('admin.admin_enfermeros.show', compact('adminEnfermero'));
    }

    
    public function edit($id)
    {
        $adminEnfermero = AdminEnfermero::with('user')->findOrFail($id);
        return view('admin.admin_enfermeros.edit', compact('adminEnfermero'));
    }

    
    public function update(Request $request, $id)
    {
        $request->merge(['rut' => RutHelper::normalizar($request->rut)]);
        $adminEnfermero = AdminEnfermero::find($id);

        $usuario = User::findOrFail($id);
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'rut' => 'required|unique:admin_enfermeros,rut,' . $adminEnfermero->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $adminEnfermero->nombre = strtoupper($request->nombre);
        $adminEnfermero->apellido = strtoupper($request->apellido);
        $adminEnfermero->rut = $request->rut;
        $adminEnfermero->save();

        $usuario = User::find($adminEnfermero->user_id);
        $usuario->name = strtoupper($request->nombre);
        $usuario->apellido = strtoupper($request->apellido);
        $usuario->rut = $request->rut;

        if($request->filled('password')){
            $usuario->password = Hash::make($request['password']);
        }
        $usuario->save();

        return redirect()->route('admin.admin_enfermeros.index')
            ->with('mensaje','Usuario Actualizado!')
            ->with('icono','success');  
    }
    
    public function destroy($id)
    {
        $adminEnfermero = AdminEnfermero::find($id);
        $user = $adminEnfermero->user;
        $user->delete();

        $adminEnfermero->delete();
        return redirect()->route('admin.admin_enfermeros.index')
            ->with('mensaje','Usuario Eliminado!')
            ->with('icono','success');
    }
}
