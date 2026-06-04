@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Registro de pacientes</h1>
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
                    <form id="form-paciente" action="{{ route('admin.pacientes.store') }}" method="POST"
                        data-spinner-color="primary">
                        @csrf

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="identificacionti_tipo">Tipo de Identificación</label>
                                    <div>
                                        <label style="margin-right: 15px;">
                                            <input type="radio" name="identificacion_tipo" value="rut" checked> Rut
                                        </label>
                                        <label style="margin-right: 15px;">
                                            <input type="radio" name="identificacion_tipo" value="pasaporte"> Pasaporte
                                        </label>
                                        <label>
                                            <input type="radio" name="identificacion_tipo" value="ficha"> N° Registro
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="rut">Identificación</label> <b>*</b>
                                    <input type="text" id="rut" name="rut" class="form-control"
                                        value="{{ old('rut') }}">
                                    <small id="rut-error" style="color:red; display:none;">RUT inválido</small>
                                    <small id="paciente-encontrado" style="display:none;color:green;font-weight:bold;"> ✓ Paciente encontrado en la base de datos</small>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="nombre">Nombre</label> <b>*</b>
                                    <input type="text" id="nombre" value="{{ old('nombre') }}" name="nombre"
                                        class="form-control" required>
                                    @error('nombre')
                                        <small style="color:red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <label for="apellido">Apellido</label> <b>*</b>
                                    <input type="text" id="apellido" value="{{ old('apellido') }}" name="apellido"
                                        class="form-control" required>
                                    @error('apellido')
                                        <small style="color:red">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form group">
                                    <a href="{{ url('admin/pacientes') }}" class="btn btn-secondary cancel-btn">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-floppy"></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (session('paciente_existente'))
                        @php
                            $paciente = session('paciente_existente');
                            $atencion = $paciente->atenciones->last();
                            $estado = $atencion->estado->nombre ?? 'sin estado';
                            $activo = $paciente->activo ? 'activo' : 'inactivo';
                            $categoriaNombre = optional($atencion->categoria)->nombre;
                            $esSinCategorizar =
                                is_null($categoriaNombre) || strtoupper($categoriaNombre) === 'SIN CATEGORIZAR';
                            $textoCategoria = $esSinCategorizar ? 'SIN CATEGORIZAR' : $categoriaNombre;
                        @endphp


                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const estadoPaciente = "{{ $estado }}";
                                const estadoLogico = "{{ $activo }}";


                                if (estadoLogico === 'activo' && ['Ingresado', 'En espera de atencion', 'En atencion',
                                        'En espera de cama'
                                    ].includes(estadoPaciente) &&
                                    "{{ $esSinCategorizar }}" === "1"
                                ) {
                                    // Modal de atención activa
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Paciente con atención activa',
                                        html: `
                                            <div style="text-align:center;">
                                                <strong>Nombre:</strong> {{ $paciente->nombre }} {{ $paciente->apellido }}<br>
                                                <strong>RUT:</strong> {{ $paciente->rut }}<br>
                                                Este paciente ya tiene una atención activa.<br>
                                                No se puede registrar una nueva atención hasta que sea dado de alta.
                                            </div>
                                        `,
                                        confirmButtonText: 'Entendido',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false
                                    });
                                } else {
                                    // Modal de paciente disponible // panel de opciones
                                    Swal.fire({
                                        title: 'Paciente ya registrado',
                                        icon: 'info',
                                        html: `
                                            <div style="text-align:center;">
                                                <strong>Nombre:</strong> {{ $paciente->nombre }} {{ $paciente->apellido }}<br>
                                                <strong>RUT:</strong> {{ $paciente->rut }}<br>
                                                <div style="display: flex; gap: 8px; justify-content: center; margin-top: 10px;">
                                                    <button id="btn-confirmar" style="background-color:#28a745; color:white; padding:6px 12px; border:none; border-radius:4px;">Agregar nueva atención</button>
                                                    <button id="btn-cancelar" style="background-color:#6c757d; color:white; padding:6px 12px; border:none; border-radius:4px;">Cancelar</button>
                                                    <button id="btn-editar" style="background-color:#EDE505; color:black; padding:6px 12px; border:none; border-radius:4px;">Editar datos</button>
                                                </div>
                                            </div>
                                        `,
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        didRender: () => {
                                            const container = Swal.getHtmlContainer();

                                            container.querySelector('#btn-confirmar')?.addEventListener('click', () => {
                                                $('#blur-overlay').show();
                                                $('#global-spinner').removeClass().addClass(
                                                    'spinner-border text-success').show();

                                                fetch("{{ route('admin.pacientes.atencionRapida', $paciente->id) }}", {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({})
                                                    })
                                                    .then(response => {
                                                        if (!response.ok) throw new Error(
                                                            'Error al registrar la atención');
                                                        return response.text();
                                                    })
                                                    .then(() => {
                                                        Swal.fire({
                                                            title: 'Atención registrada',
                                                            icon: 'success',
                                                            timer: 1500,
                                                            showConfirmButton: false
                                                        }).then(() => {
                                                            window.location.href =
                                                                "{{ route('admin.pacientes.index') }}";
                                                        });
                                                    })
                                                    .catch(error => {
                                                        $('#blur-overlay').hide();
                                                        $('#global-spinner').hide();
                                                        Swal.fire({
                                                            title: 'Error',
                                                            text: error.message,
                                                            icon: 'error'
                                                        });
                                                    });
                                            });

                                            container.querySelector('#btn-cancelar')?.addEventListener('click', () => {
                                                Swal.close();
                                            });

                                            container.querySelector('#btn-editar')?.addEventListener('click', () => {
                                                Swal.fire({
                                                    title: 'Editar datos del paciente',
                                                    html: `
                                                        <input type="text" id="nuevo-nombre" value="{{ $paciente->nombre }}" class="form-control mb-2" placeholder="Nombre" required>
                                                        <input type="text" id="nuevo-apellido" value="{{ $paciente->apellido }}" class="form-control mb-2" placeholder="Apellido" required>
                                                    `,
                                                    showConfirmButton: true,
                                                    confirmButtonText: 'Guardar cambios',
                                                    showCancelButton: true,
                                                    cancelButtonText: 'Cancelar',
                                                    allowOutsideClick: false,
                                                    allowEscapeKey: false,
                                                    preConfirm: () => {
                                                        const nombre = document.getElementById(
                                                            'nuevo-nombre').value;
                                                        const apellido = document.getElementById(
                                                            'nuevo-apellido').value;

                                                        return fetch(
                                                                "{{ route('admin.pacientes.actualizarDatos', $paciente->id) }}", {
                                                                    method: 'PUT',
                                                                    headers: {
                                                                        'Content-Type': 'application/json',
                                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                    },
                                                                    body: JSON.stringify({
                                                                        nombre,
                                                                        apellido
                                                                    })
                                                                })
                                                            .then(response => {
                                                                if (!response.ok) throw new Error(
                                                                    'Error al guardar los datos'
                                                                );
                                                                return response.json();
                                                            })
                                                            .catch(error => {
                                                                Swal.showValidationMessage(
                                                                    `Error: ${error}`);
                                                            });
                                                    }
                                                }).then(result => {
                                                    if (result.isConfirmed) {
                                                        Swal.fire({
                                                            title: 'Datos actualizados',
                                                            icon: 'success',
                                                            timer: 1500,
                                                            showConfirmButton: false,
                                                            allowOutsideClick: false,
                                                            allowEscapeKey: false
                                                        }).then(() => {
                                                            mostrarModalAtencion(result.value
                                                                .paciente);
                                                        });
                                                    }
                                                    if (result.dismiss === Swal.DismissReason.cancel) {
                                                        mostrarModalAtencion(@json($paciente));
                                                    }
                                                });
                                            });
                                        }
                                    });
                                }

                                // Función para reabrir el modal principal luego de editar
                                function mostrarModalAtencion(paciente) {

                                    Swal.fire({
                                        title: 'Paciente ya registrado',
                                        icon: 'info',
                                        html: `
            <div style="text-align:center;">
                <strong>Nombre:</strong> ${paciente.nombre} ${paciente.apellido}<br>
                <strong>RUT:</strong> ${paciente.rut}<br>
                <strong>Última atención:</strong> ${paciente.ultima_categoria ?? 'sin categorizar'}<br>
                <strong>Total de atenciones:</strong> ${paciente.total_atenciones}<br><br>

                <div style="display:flex; gap:8px; justify-content:center; margin-top:10px;">
                    <button id="btn-confirmar"
                        style="background-color:#28a745;color:white;padding:6px 12px;border:none;border-radius:4px;">
                        Agregar nueva atención
                    </button>

                    <button id="btn-cancelar"
                        style="background-color:#6c757d;color:white;padding:6px 12px;border:none;border-radius:4px;">
                        Cancelar
                    </button>

                    <button id="btn-editar"
                        style="background-color:#EDE505;color:black;padding:6px 12px;border:none;border-radius:4px;">
                        Editar datos
                    </button>
                </div>
            </div>
        `,
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,

                                        didRender: () => {

                                            const container = Swal.getHtmlContainer();

                                            // BOTÓN NUEVA ATENCIÓN
                                            container.querySelector('#btn-confirmar')
                                                ?.addEventListener('click', () => {

                                                    fetch("{{ route('admin.pacientes.atencionRapida', $paciente->id) }}", {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                            },
                                                            body: JSON.stringify({})
                                                        })
                                                        .then(response => {
                                                            if (!response.ok)
                                                                throw new Error('Error al registrar atención');

                                                            return response.text();
                                                        })
                                                        .then(() => {

                                                            Swal.fire({
                                                                title: 'Atención registrada',
                                                                icon: 'success',
                                                                timer: 1500,
                                                                showConfirmButton: false
                                                            }).then(() => {

                                                                window.location.href =
                                                                    "{{ route('admin.pacientes.index') }}";
                                                            });

                                                        });

                                                });

                                            // BOTÓN CANCELAR
                                            container.querySelector('#btn-cancelar')
                                                ?.addEventListener('click', () => {

                                                    Swal.close();

                                                });

                                            // BOTÓN EDITAR
                                            container.querySelector('#btn-editar')
                                                ?.addEventListener('click', () => {

                                                    Swal.fire({
                                                        title: 'Editar datos del paciente',

                                                        html: `
                            <input type="text"
                                id="nuevo-nombre"
                                value="${paciente.nombre}"
                                class="form-control mb-2"
                                placeholder="Nombre">

                            <input type="text"
                                id="nuevo-apellido"
                                value="${paciente.apellido}"
                                class="form-control mb-2"
                                placeholder="Apellido">
                        `,

                                                        showCancelButton: true,
                                                        confirmButtonText: 'Guardar cambios',

                                                        preConfirm: () => {

                                                            const nombre =
                                                                document.getElementById('nuevo-nombre')
                                                                .value;

                                                            const apellido =
                                                                document.getElementById('nuevo-apellido')
                                                                .value;

                                                            return fetch(
                                                                    "{{ route('admin.pacientes.actualizarDatos', $paciente->id) }}", {

                                                                        method: 'PUT',

                                                                        headers: {
                                                                            'Content-Type': 'application/json',
                                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                                        },

                                                                        body: JSON.stringify({
                                                                            nombre,
                                                                            apellido
                                                                        })

                                                                    })
                                                                .then(response => {

                                                                    if (!response.ok)
                                                                        throw new Error('Error');

                                                                    return response.json();

                                                                });

                                                        }

                                                    }).then(result => {

                                                        if (result.isConfirmed) {

                                                            mostrarModalAtencion(result.value.paciente);

                                                        }
                                                    });

                                                });

                                        }
                                    });
                                }
                            });
                        </script>
                    @endif
                    {{-- BÚSQUEDA AUTOMÁTICA DE PACIENTE --}}
                    <script>
                        $(document).ready(function() {

                            let timeoutBusqueda;

                            $('#rut').on('input', function() {

                                clearTimeout(timeoutBusqueda);

                                let identificacion = $(this).val().trim();
                                let tipo = $('input[name="identificacion_tipo"]:checked').val();

                                // Limpiar si está vacío o muy corto
                                if (identificacion.length < 3) {

                                    $('#nombre').val('');
                                    $('#apellido').val('');

                                    $('#paciente-encontrado').hide();

                                    return;
                                }

                                timeoutBusqueda = setTimeout(function() {

                                    $.ajax({
                                        url: "{{ route('admin.pacientes.buscarIdentificacion') }}",
                                        method: "GET",

                                        data: {
                                            identificacion: identificacion,
                                            tipo: tipo
                                        },

                                        success: function(response) {

                                            if (response.encontrado) {

                                                $('#nombre').val(response.paciente.nombre);
                                                $('#apellido').val(response.paciente.apellido);

                                                $('#paciente-encontrado').show();

                                            } else {

                                                $('#nombre').val('');
                                                $('#apellido').val('');

                                                $('#paciente-encontrado').hide();
                                            }
                                        },

                                        error: function(xhr, status, error) {

                                            console.error('Error al buscar paciente:', error);

                                            $('#paciente-encontrado').hide();
                                        }
                                    });

                                }, 300);

                            });

                            // Limpiar todo cuando cambie el tipo de identificación
                            $('input[name="identificacion_tipo"]').change(function() {

                                $('#rut').val('');
                                $('#nombre').val('');
                                $('#apellido').val('');

                                $('#paciente-encontrado').hide();

                            });

                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
