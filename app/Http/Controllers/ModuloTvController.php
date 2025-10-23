<?php

namespace App\Http\Controllers;

use App\Models\ModuloTv;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Rules\RutChileno;
use App\Helpers\RutHelper;
use PhpParser\Node\Expr\AssignOp\Mod;

class ModuloTvController extends Controller
{
    
    public function index()
    {
        $moduloTvs = ModuloTv::with('user')->get();
        return view('admin.moduloTv.index', compact('moduloTvs'));
    }

    
    public function create()
    {
        return view('admin.moduloTV.create');
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
        
        $moduloTvs = new ModuloTv();
        $moduloTvs->user_id = $usuario->id;
        $moduloTvs->nombre = strtoupper($request->nombre);
        $moduloTvs->apellido = strtoupper($request->apellido);
        $moduloTvs->rut = $rutNormalizado;
        $moduloTvs->save();

        $usuario->assignRole('panel');

        return redirect()->route('admin.moduloTV.index')
            ->with('mensaje', 'Enfermero creado exitosamente.')
            ->with('icono','success');
    }
    
    public function show($id)
    {
        //Retorna la vista para mostrar un moduloTV
        $moduloTv = ModuloTv::with('user')->findOrFail($id);
        return view('admin.moduloTV.show', compact('moduloTv'));
    }
 
    public function edit($id)
    {
        $moduloTv = ModuloTV::with('user')->findOrFail($id);
        return view('admin.moduloTV.edit', compact('moduloTv'));
    }
    
    public function update(Request $request, $id)
    {
        //Valida los datos del formulario de edición
        //Busca el moduloTV por ID y lanza una excepción si no se encuentra
        //Actualiza los datos del moduloTV y del usuario asociado
        //Si se proporciona una nueva contraseña, la actualiza
        $request->merge(['rut' => RutHelper::normalizar($request->rut)]);
        $moduloTv = ModuloTv::find($id);

        $usuario = User::findOrFail($id);
        $request->validate([
            'nombre' => 'required|max:50',
            'apellido' => 'required|max:50',
            'rut' => 'required|max:10|unique:modulo_tvs,rut,' . $moduloTv->id,
            'password' => 'nullable|min:8|confirmed',
        ]);  

        $moduloTv->nombre = strtoupper($request->nombre);
        $moduloTv->apellido = strtoupper($request->apellido);
        $moduloTv->rut = $request->rut;
        $moduloTv->save();

        $usuario = User::find($moduloTv->user_id);
        $usuario->name = strtoupper($request->nombre);
        $usuario->apellido = strtoupper($request->apellido);
        $usuario->rut = $request->rut;
        if($request->filled('password')) {
            // Solo actualiza la contraseña si se ha proporcionado una nueva
            $usuario->password = Hash::make($request['password']);
        }        
        $usuario->save();

        return redirect()->route('admin.moduloTV.index')
            ->with('mensaje', 'Datos actualizado exitosamente.')
            ->with('icono','success');
    }

    
    public function destroy($id)
    {
        //Elimina el moduloTV y el usuario asociado
        $moduloTv = ModuloTv::find($id);
        //Eliminar el usuario asociado, estamos trayendo del modelo moduloTV hacia el usuario
        $user = $moduloTv->user;
        $user->delete();
        
        //Eliminar el moduloTV
        $moduloTv->delete();

        return redirect()->route('admin.moduloTV.index')
            ->with('mensaje', 'Enfermero eliminado exitosamente.')
            ->with('icono','success');
    }
}
