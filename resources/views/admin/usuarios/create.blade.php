@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Registro de usuarios</h1>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Completar los datos</h3>
                    <div class="card-tools">

                    </div>
                </div>
                <div class="card-body" style="display: block;">
                    <form action="{{ route('admin.usuarios.store') }}" method="POST" data-spinner-color="primary">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="rut">Rut</label> <b>*</b>
                                    <input placeholder="RUT (sin puntos ni guión)" type="text" name="rut" id="rut" class="form-control"
                                        value="{{ old('rut') }}" required>
                                    <small id="rut-error" style="color:red; display:none;">RUT inválido</small>
                                    @error('rut')
                                        <small style="color:red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="">Nombre</label> <b>*</b>
                                    <input type="text" value="{{ old('name') }}" name="name" class="form-control" required>
                                    @error('name')
                                        <small style="color:red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="">Apellido</label> <b>*</b>
                                    <input type="text" value="{{ old('apellido') }}" name="apellido" class="form-control" required>
                                    @error('apellido')
                                        <small style="color:red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="">Contraseña</label> <b>*</b>
                                    <input placeholder="Contraseña debe contener 8 caracteres" type="password" name="password" class="form-control" required>
                                    @error('password')
                                        <small style="color:red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="">Verificación Contraseña</label> <b>*</b>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                    @error('password_confirmation')
                                        <small style="color:red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary cancel-btn">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-floppy"></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection