<?php

namespace App\Http\Controllers;

use App\Models\AdminEnfermero;
use App\Models\AdminUrgencia;
use App\Models\Admision;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Enfermero;
use App\Models\Paciente;
use App\Models\Categoria;
use App\Models\ModuloTv;

class AdministradorController extends Controller
{
    public function index(){
        $total_usuarios = User::count();
        $total_admisiones = Admision::count();
        $total_enfermeros = Enfermero::count();
        $total_pacientes = Paciente::count();    
        $total_admin_urgencias = AdminUrgencia::count();
        $total_admin_enfermeros = AdminEnfermero::count();
        $total_moduloTv = ModuloTv::count();
        $categorias = Categoria::withCount(['pacientes'])->get();  

        $usuarios = User::all();
        $admisiones = Admision::all();
        $enfermeros = Enfermero::all();
        $pacientes = Paciente::all();
        $admin_urgencias = AdminUrgencia::all(); 
        $admin_enfermeros = AdminEnfermero::all();
        $moduloTv = ModuloTv::all();
              

        return view('admin.index', compact('total_usuarios', 'total_admisiones', 'total_enfermeros', 'total_pacientes', 'total_admin_urgencias', 'total_admin_enfermeros', 'total_moduloTv'  ,'categorias', 'usuarios', 'admisiones', 'enfermeros' ,'pacientes', 'admin_enfermeros', 'admin_urgencias'));
        
    }

    
}
