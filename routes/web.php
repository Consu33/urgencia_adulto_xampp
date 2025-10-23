<?php

use App\Http\Controllers\PacienteController;
use App\Http\Controllers\AdminUrgenciaController;
use App\Http\Controllers\AdminEnfermeroController;
use App\Http\Controllers\AdmisionController;
use App\Http\Controllers\EnfermeroController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EstadoPacienteController;
use App\Http\Controllers\AtencionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {   
        return view('index');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//Rutas para el administrador
Route::get('/admin', [App\Http\Controllers\AdministradorController::class, 'index'])->name('admin.index')->middleware('auth');

//Rutas para el administrador - usuarios admisiones
//GET para mostrar los datos - POST para insertar los datos - PUT para actualizar los datos - DELETE para eliminar los datos
Route::get('/admin/usuarios', [App\Http\Controllers\UsuarioController::class, 'index'])->name('admin.usuarios.index')->middleware('auth', 'can:admin.usuarios.index');
Route::get('/admin/usuarios/create', [App\Http\Controllers\UsuarioController::class, 'create'])->name('admin.usuarios.create')->middleware('auth', 'can:admin.usuarios.create');
Route::post('/admin/usuarios/create', [App\Http\Controllers\UsuarioController::class, 'store'])->name('admin.usuarios.store')->middleware('auth', 'can:admin.usuarios.store');
Route::get('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'show'])->name('admin.usuarios.show')->middleware('auth', 'can:admin.usuarios.show');
Route::get('/admin/usuarios/{id}/edit', [App\Http\Controllers\UsuarioController::class, 'edit'])->name('admin.usuarios.edit')->middleware('auth', 'can:admin.usuarios.edit');
Route::put('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'update'])->name('admin.usuarios.update')->middleware('auth', 'can:admin.usuarios.update');
Route::delete('/admin/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy')->middleware('auth', 'can:admin.usuarios.destroy');

//Rutas para el administrador - usuarios admin_urgencias
Route::get('/admin/admin_urgencias', [App\Http\Controllers\AdminUrgenciaController::class, 'index'])->name('admin.admin_urgencias.index')->middleware('auth', 'can:admin.admin_urgencias.index');
Route::get('/admin/admin_urgencias/create', [App\Http\Controllers\AdminUrgenciaController::class, 'create'])->name('admin.admin_urgencias.create')->middleware('auth', 'can:admin.admin_urgencias.create');
Route::post('/admin/admin_urgencias/create', [App\Http\Controllers\AdminUrgenciaController::class, 'store'])->name('admin.admin_urgencias.store')->middleware('auth', 'can:admin.admin_urgencias.store');
Route::get('/admin/admin_urgencias/{id}', [App\Http\Controllers\AdminUrgenciaController::class, 'show'])->name('admin.admin_urgencias.show')->middleware('auth', 'can:admin.admin_urgencias.show');
Route::get('/admin/admin_urgencias/{id}/edit', [App\Http\Controllers\AdminUrgenciaController::class, 'edit'])->name('admin.admin_urgencias.edit')->middleware('auth', 'can:admin.admin_urgencias.edit');
Route::put('/admin/admin_urgencias/{id}', [App\Http\Controllers\AdminUrgenciaController::class, 'update'])->name('admin.admin_urgencias.update')->middleware('auth', 'can:admin.admin_urgencias.update');
Route::delete('/admin/admin_urgencias/{id}', [App\Http\Controllers\AdminUrgenciaController::class, 'destroy'])->name('admin.admin_urgencias.destroy')->middleware('auth', 'can:admin.admin_urgencias.destroy');

//Rutas para el administrador - usuarios admin_enfermeros
Route::get('/admin/admin_enfermeros', [App\Http\Controllers\AdminEnfermeroController::class, 'index'])->name('admin.admin_enfermeros.index')->middleware('auth', 'can:admin.admin_enfermeros.index');
Route::get('/admin/admin_enfermeros/create', [App\Http\Controllers\AdminEnfermeroController::class, 'create'])->name('admin.admin_enfermeros.create')->middleware('auth', 'can:admin.admin_enfermeros.create');
Route::post('/admin/admin_enfermeros/create', [App\Http\Controllers\AdminEnfermeroController::class, 'store'])->name('admin.admin_enfermeros.store')->middleware('auth', 'can:admin.admin_enfermeros.store');
Route::get('/admin/admin_enfermeros/{id}', [App\Http\Controllers\AdminEnfermeroController::class, 'show'])->name('admin.admin_enfermeros.show')->middleware('auth', 'can:admin.admin_enfermeros.show');
Route::get('/admin/admin_enfermeros/{id}/edit', [App\Http\Controllers\AdminEnfermeroController::class, 'edit'])->name('admin.admin_enfermeros.edit')->middleware('auth', 'can:admin.admin_enfermeros.show');
Route::put('/admin/admin_enfermeros/{id}', [App\Http\Controllers\AdminEnfermeroController::class, 'update'])->name('admin.admin_enfermeros.update')->middleware('auth', 'can:admin.admin_enfermeros.update');
Route::delete('/admin/admin_enfermeros/{id}', [App\Http\Controllers\AdminEnfermeroController::class, 'destroy'])->name('admin.admin_enfermeros.destroy')->middleware('auth', 'can:admin.admin_enfermeros.destroy');

//Rutas para el administrador - usuarios admisiones
Route::get('/admin/admisiones', [App\Http\Controllers\AdmisionController::class, 'index'])->name('admin.admisiones.index')->middleware('auth', 'can:admin.admisiones.index');
Route::get('/admin/admisiones/create', [App\Http\Controllers\AdmisionController::class, 'create'])->name('admin.admisiones.create')->middleware('auth', 'can:admin.admisiones.create');
Route::post('/admin/admisiones/create', [App\Http\Controllers\AdmisionController::class, 'store'])->name('admin.admisiones.store')->middleware('auth', 'can:admin.admisiones.store');
Route::get('/admin/admisiones/{id}', [App\Http\Controllers\AdmisionController::class, 'show'])->name('admin.admisiones.show')->middleware('auth', 'can:admin.admisiones.show');
Route::get('/admin/admisiones/{id}/edit', [App\Http\Controllers\AdmisionController::class, 'edit'])->name('admin.admisiones.edit')->middleware('auth', 'can:admin.admisiones.edit');
Route::put('/admin/admisiones/{id}', [App\Http\Controllers\AdmisionController::class, 'update'])->name('admin.admisiones.update')->middleware('auth', 'can:admin.admisiones.update');
Route::delete('/admin/admisiones/{id}', [App\Http\Controllers\AdmisionController::class, 'destroy'])->name('admin.admisiones.destroy')->middleware('auth', 'can:admin.admisiones.destroy');

//Rutas para el administrador - usuarios enfermeros
Route::get('/admin/enfermeros', [App\Http\Controllers\EnfermeroController::class, 'index'])->name('admin.enfermeros.index')->middleware('auth', 'can:admin.enfermeros.index');
Route::get('/admin/enfermeros/create', [App\Http\Controllers\EnfermeroController::class, 'create'])->name('admin.enfermeros.create')->middleware('auth', 'can:admin.enfermeros.create');
Route::post('/admin/enfermeros/create', [App\Http\Controllers\EnfermeroController::class, 'store'])->name('admin.enfermeros.store')->middleware('auth', 'can:admin.enfermeros.store');
Route::get('/admin/enfermeros/{id}', [App\Http\Controllers\EnfermeroController::class, 'show'])->name('admin.enfermeros.show')->middleware('auth', 'can:admin.enfermeros.show');
Route::get('/admin/enfermeros/{id}/edit', [App\Http\Controllers\EnfermeroController::class, 'edit'])->name('admin.enfermeros.edit')->middleware('auth', 'can:admin.enfermeros.edit');
Route::put('/admin/enfermeros/{id}', [App\Http\Controllers\EnfermeroController::class, 'update'])->name('admin.enfermeros.update')->middleware('auth', 'can:admin.enfermeros.update');
Route::delete('/admin/enfermeros/{id}', [App\Http\Controllers\EnfermeroController::class, 'destroy'])->name('admin.enfermeros.destroy')->middleware('auth', 'can:admin.enfermeros.destroy');

//Rutas para el administrador - usuarios pacientes
Route::get('/admin/pacientes', [App\Http\Controllers\PacienteController::class, 'index'])->name('admin.pacientes.index')->middleware('auth', 'can:admin.pacientes.index');
Route::get('/admin/pacientes/create', [App\Http\Controllers\PacienteController::class, 'create'])->name('admin.pacientes.create')->middleware('auth', 'can:admin.pacientes.create');
Route::post('/admin/pacientes/create', [App\Http\Controllers\PacienteController::class, 'store'])->name('admin.pacientes.store')->middleware('auth', 'can:admin.pacientes.store');
Route::get('/admin/pacientes/{id}', [App\Http\Controllers\PacienteController::class, 'show'])->name('admin.pacientes.show')->middleware('auth', 'can:admin.pacientes.show');
Route::get('/admin/pacientes/{id}/edit', [App\Http\Controllers\PacienteController::class, 'edit'])->name('admin.pacientes.edit')->middleware('auth', 'can:admin.pacientes.edit');
Route::put('/admin/pacientes/{id}', [App\Http\Controllers\PacienteController::class, 'update'])->name('admin.pacientes.update')->middleware('auth', 'can:admin.pacientes.update');
Route::delete('/admin/pacientes/{id}', [App\Http\Controllers\PacienteController::class, 'destroy'])->name('admin.pacientes.destroy')->middleware('auth', 'can:admin.pacientes.destroy');
Route::post('/admin/pacientes/{id}/update-category', [App\Http\Controllers\EstadoPacienteController::class, 'updateCategory'])->name('admin.pacientes.update-category')->middleware('auth', 'can:admin.pacientes.condition');

//rutas de atenciones para pacientes ya en la bd
Route::get('/admin/atenciones/create/{paciente_id}', [App\Http\Controllers\AtencionController::class, 'create'])->name('admin.atenciones.create')->middleware('auth', 'can:admin.atenciones.create');
Route::post('/admin/atenciones', [App\Http\Controllers\AtencionController::class, 'store'])->name('admin.atenciones.store')->middleware('auth', 'can:admin.atenciones.store');
Route::post('/admin/pacientes/{paciente}/atencion-rapida', [PacienteController::class, 'atencionRapida'])->name('admin.pacientes.atencionRapida');
Route::put('/admin/pacientes/{paciente}/actualizar-datos', [PacienteController::class, 'actualizarDatos'])->name('admin.pacientes.actualizarDatos');

//Rutas para el EstadoPaciente
Route::get('/admin/condition', [App\Http\Controllers\EstadoPacienteController::class, 'condition'])->name('admin.pacientes.condition')->middleware('auth', 'can:admin.pacientes.condition');
Route::post('/admin/pacientes/{id}/update-category', [App\Http\Controllers\EstadoPacienteController::class, 'updateCategory'])->name('admin.pacientes.updateCategory')->middleware('auth', 'can:admin.pacientes.updateCategory');
Route::get('/admin/panel', [App\Http\Controllers\EstadoPacienteController::class, 'index'])->name('admin.panel.index');
Route::get('/admin/panel/dinamico', [App\Http\Controllers\EstadoPacienteController::class, 'panelDinamico'])->name('admin.panel.dinamico')->middleware('auth', 'can:admin.panel.index');

//Rutas para el panel TV de urgencias
Route::get('/admin/panel_urgencia', [App\Http\Controllers\EstadoPacienteController::class, 'tv'])->name('admin.panel.tv')->middleware('auth', 'can:admin.panel.tv');
Route::get('/admin/panel_urgencia/dinamico', [EstadoPacienteController::class, 'panelUrgenciaDinamico'])->name('admin.panel_urgencia.dinamico');
Route::get('/admin/moduloTV', [App\Http\Controllers\ModuloTvController::class, 'index'])->name('admin.moduloTV.index')->middleware('auth', 'can:admin.moduloTV.index');
Route::get('/admin/moduloTV/create', [App\Http\Controllers\ModuloTvController::class, 'create'])->name('admin.moduloTV.create')->middleware('auth', 'can:admin.moduloTV.create');
Route::post('/admin/moduloTV/create', [App\Http\Controllers\ModuloTvController::class, 'store'])->name('admin.moduloTV.store')->middleware('auth', 'can:admin.moduloTV.store');
Route::get('/admin/moduloTV/{id}', [App\Http\Controllers\ModuloTvController::class, 'show'])->name('admin.moduloTV.show')->middleware('auth', 'can:admin.moduloTV.show');
Route::get('/admin/moduloTV/{id}/edit', [App\Http\Controllers\ModuloTvController::class, 'edit'])->name('admin.moduloTV.edit')->middleware('auth', 'can:admin.moduloTV.edit');
Route::put('/admin/moduloTV/{id}', [App\Http\Controllers\ModuloTvController::class, 'update'])->name('admin.moduloTV.update')->middleware('auth', 'can:admin.moduloTV.update');
Route::delete('/admin/moduloTV/{id}', [App\Http\Controllers\ModuloTvController::class, 'destroy'])->name('admin.moduloTV.destroy')->middleware('auth', 'can:admin.moduloTV.destroy');

