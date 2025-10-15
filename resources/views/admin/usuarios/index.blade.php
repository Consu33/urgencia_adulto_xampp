@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Usuarios Sistema Urgencia</h1>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-10">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Usuarios Registrados</h3>
                    <div class="card-tools">
                        <a href="{{ url('admin/usuarios/create') }}" class="btn btn-primary access-btn"><i class="bi bi-plus"></i>
                            Registrar Nuevo Usuario
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-striped table-sm display nowrap compact" style="width:100%">
                        <thead style="background-color: #c0c0c0">
                            <tr>
                                <td style="text-align:center">Número</td>
                                <td style="text-align:center">Rol</td>
                                <td style="text-align:center">Nombre</td>
                                <td style="text-align:center">Apellido</td>
                                <td style="text-align:center">Rut</td>
                                <td style="text-align:center">Acciones</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $contador = 1; ?>
                            @foreach ($usuarios as $usuario)
                                <tr>
                                    <td style="text-align:center">{{ $loop->iteration }}</td>
                                    <td style="text-align">{{ $usuario->roles->pluck('name')->join(', ') }}</td>
                                    <td style="text-align:center">{{ $usuario->name }}</td>
                                    <td style="text-align:center">{{ $usuario->apellido }}</td>     
                                    <td style="text-align:center">{{ $usuario->rut }}</td>                                
                                    <td style="text-align:center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary btn-sm spinner-btn"
                                                data-bs-toggle="modal" data-bs-target="#showModal-{{ $usuario->id }}"
                                                title="Visualizar registros">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-sm spinner-btn"
                                                data-bs-toggle="modal" data-bs-target="#editModal-{{ $usuario->id }}"
                                                title="Visualizar registros">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm spinner-btn"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $usuario->id }}"
                                                title="Eliminar registros">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>                                        
                                    </td>
                                </tr>
                                {{-- Modal show--}}
                                <div class="modal fade" id="showModal-{{ $usuario->id }}" tabindex="-1" aria-labelledby="showModalLabel-{{ $usuario->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="showModalLabel-{{ $usuario->id }}">Registros</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Rut</label>
                                                            <input type="text" value="{{ $usuario->rut }}" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Nombre</label>
                                                            <input type="text" value="{{ $usuario->name }}" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Apellido</label>
                                                            <input type="text" value="{{ $usuario->apellido }}" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Modal editar --}}
                                <div class="modal fade" id="editModal-{{ $usuario->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $usuario->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ url('/admin/usuarios/' . $usuario->id) }}" method="POST" data-spinner-color="success">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="editModalLabel-{{ $usuario->id }}">¿Estás Seguro de Editar el Registro?</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Rut</label>
                                                                <input type="text" value="{{ $usuario->rut }}" name="rut" class="form-control" required>
                                                                @error('rut')
                                                                    <small style="color:red">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Nombre</label>
                                                                <input type="text" value="{{ $usuario->name }}" name="name" class="form-control" required>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Apellido</label>
                                                                <input type="text" value="{{ $usuario->apellido }}" name="apellido" class="form-control" required>
                                                            </div>
                                                        </div>
                                                    </div>  
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form group">
                                                                <label for="">Contraseña</label> 
                                                                <input type="password" name="password" class="form-control">
                                                                @error('password')
                                                                    <small style="color:red">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form group">
                                                                <label for="">Verificación Contraseña</label>
                                                                <input type="password" name="password_confirmation" class="form-control" >
                                                                @error('password_confirmation')
                                                                    <small style="color:red">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>                                            
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-pencil-fill"></i> Registro Editado
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- Modal eliminar --}}
                                <div class="modal fade" id="deleteModal-{{ $usuario->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $usuario->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ url('/admin/usuarios/' . $usuario->id) }}" method="POST" data-spinner-color="danger">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteModalLabel-{{ $usuario->id }}">¿Estás Seguro de Eliminar el Registro?</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Rut</label>
                                                                <input type="text" value="{{ $usuario->rut }}" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Nombre</label>
                                                                <input type="text" value="{{ $usuario->name }}" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>                                                    
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Apellido</label>
                                                                <input type="text" value="{{ $usuario->apellido }}" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>                                          
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash"></i> Registro Eliminado
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                    <script>
                        $(document).ready(function() {
                            let table = $('#example1').DataTable({
                                pageLength: 10,
                                responsive: true,
                                autoWidth: false,
                                dom: '<"row mb-2"<"col-sm-6"B><"col-sm-6"f>>' + '<"row"<"col-sm-12"tr>>' + '<"row mt-2"<"col-sm-5"i><"col-sm-7"p>>',
                                language: {
                                    emptyTable: "No hay información",
                                    info: "Mostrando _START_ a _END_ de _TOTAL_ Usuarios",
                                    infoEmpty: "Mostrando 0 a 0 de 0 Usuarios",
                                    infoFiltered: "(Filtrado de _MAX_ total Usuarios)",
                                    lengthMenu: "Mostrar _MENU_ Usuarios",
                                    loadingRecords: "Cargando...",
                                    processing: "Procesando...",
                                    search: "Buscador:",
                                    zeroRecords: "Sin resultados encontrados",
                                    paginate: {
                                        first: "Primero",
                                        last: "Último",
                                        next: "Siguiente",
                                        previous: "Anterior"
                                    },
                                    buttons: {
                                        copy: "Copiar",
                                        colvis: "Visor de columnas",
                                        print: "Imprimir"
                                    }
                                },
                                buttons: [{
                                        extend: 'collection',
                                        text: 'Reportes',
                                        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                                    },
                                    {
                                        extend: 'colvis',
                                        text: 'Visor de columnas'
                                    }
                                ]
                            });

                            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
