@extends('layouts.admin')

@section('content')
    <div class="row">
        <h1>Categorización de Pacientes</h1>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Pacientes Registrados</h3>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-striped table-sm display nowrap compact" style="width:100%">
                        <thead style="background-color: #c0c0c0">
                            <tr>
                                <td style="text-align:center">Número</td>
                                <td style="text-align:center">Rut</td>
                                <td style="text-align:center">Nombre</td>
                                <td style="text-align:center">Apellido</td>
                                <td style="text-align:center">Categoría</td>
                                <td style="text-align:center">Estado</td>
                                <td style="text-align:center">Acciones</td>
                                <td style="text-align:center">Eliminación de Paciente</td>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pacientes as $paciente)
                                <tr>
                                    <td style="text-align:center">{{ $loop->iteration }}</td>
                                    <td style="text-align:center">{{ $paciente->rut }}</td>
                                    <td style="text-align:center">{{ $paciente->nombre }}</td>
                                    <td style="text-align:center">{{ $paciente->apellido }}</td>

                                    {{-- Categoría --}}
                                    <td style="text-align:center">
                                        @php
                                            $categoriaSeleccionada = $categorias->firstWhere(
                                                'id',
                                                $paciente->categoria_id,
                                            );
                                            $colorClase = $categoriaSeleccionada
                                                ? 'bg-' . str_replace('bg-', '', $categoriaSeleccionada->color)
                                                : 'bg-light';
                                        @endphp

                                        <select name="categoria_id"
                                            class="form-select form-select-sm text-center rounded {{ $colorClase }}"
                                            onchange="actualizarEstado({{ $paciente->id }}); actualizarColor(this);"
                                            id="categoria-{{ $paciente->id }}"
                                            data-original="{{ $paciente->categoria_id }}">
                                            <option value="" {{ is_null($paciente->categoria_id) ? 'selected' : '' }}
                                                disabled> - </option>
                                            @foreach ($categorias as $categoria)
                                                <option value="{{ $categoria->id }}"
                                                    {{ $paciente->categoria_id == $categoria->id ? 'selected' : '' }}>
                                                    {{ $categoria->codigo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    {{-- Estado --}}

                                    <td style="text-align:center">
                                        <select name="estado_id"
                                            class="form-select form-select-sm text-center rounded bg-light"
                                            onchange="actualizarEstado({{ $paciente->id }})"
                                            id="estado-{{ $paciente->id }}">
                                            <option value="" {{ is_null($paciente->estado_id) ? 'selected' : '' }}
                                                disabled> - </option>
                                            @foreach ($estados as $estado)
                                                <option value="{{ $estado->id }}"
                                                    {{ $paciente->estado_id == $estado->id ? 'selected' : '' }}>
                                                    {{ $estado->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td style="text-align:center">
                                        <span id="feedback-{{ $paciente->id }}"></span>
                                    </td>

                                    {{-- Modal de eliminacion --}}

                                    <td style="text-align: center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-danger btn-sm spinner-btn"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $paciente->id }}"
                                                title="Eliminar registros">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="deleteModal-{{ $paciente->id }}" tabindex="-1"
                                    aria-labelledby="deleteModalLabel-{{ $paciente->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ url('admin/pacientes/' . $paciente->id) }}" method="POST"
                                            data-spinner-color="danger">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteModalLabel-{{ $paciente->id }}">
                                                        ¿Estás Seguro de Eliminar el Registro?</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Rut</label>
                                                                <input type="text" value="{{ $paciente->rut }}"
                                                                    class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Nombre y Apellido</label>
                                                                <input type="text"
                                                                    value="{{ $paciente->nombre . ' ' . $paciente->apellido }}"
                                                                    class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Volver</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash"></i>Eliminar
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Scripts --}}
                    <script>
                        function actualizarEstado(pacienteId) {
                            const categoriaSelect = document.getElementById(`categoria-${pacienteId}`);
                            const estadoSelect = document.getElementById(`estado-${pacienteId}`);
                            const feedback = document.getElementById(`feedback-${pacienteId}`);

                            const categoriaId = categoriaSelect.value;
                            const estadoId = estadoSelect.value;

                            categoriaSelect.disabled = true;
                            estadoSelect.disabled = true;
                            feedback.innerHTML = `<span class="spinner-border spinner-border-sm text-primary" role="status"></span>`;

                            fetch(`/admin/pacientes/${pacienteId}/update-category`, {
                                //fetch({{ url('admin/pacientes') }}/${pacienteId}/update-category, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        categoria_id: categoriaId,
                                        estado_id: estadoId
                                    })
                                })
                                .then(response => {
                                    if (!response.ok) throw new Error('Error en la actualización');
                                    return response.json();
                                })
                                .then(data => {
                                    // ✅ Puedes mostrar el tiempo estimado si lo deseas
                                    const mensaje = `✔️ Actualizado (${data.tiempoEstimado} min estimado)`;
                                    feedback.innerHTML = `<span class="text-success">✔️ Actualizado</span>`;
                                    setTimeout(() => {
                                        feedback.innerHTML = '';
                                    }, 3000);
                                })
                                .catch(error => {
                                    feedback.innerHTML = `<span class="text-danger">❌ Error</span>`;
                                })
                                .finally(() => {
                                    categoriaSelect.disabled = false;
                                    estadoSelect.disabled = false;
                                });
                        }
                    </script>

                    <script>
                        // Colores dinámicos por categoría
                        const categoriaColores = {
                            @foreach ($categorias as $categoria)
                                "{{ $categoria->id }}": "{{ str_replace('bg-', '', $categoria->color) }}",
                            @endforeach
                        };

                        function actualizarColor(select) {
                            const categoriaId = select.value;
                            const color = categoriaColores[categoriaId] || 'light';
                            // Elimina clases de fondo anteriores
                            select.classList.forEach(cls => {
                                if (cls.startsWith('bg-')) select.classList.remove(cls);
                            });

                            select.classList.add('bg-' + color);

                            if (['dark', 'primary', 'danger', 'success'].includes(color)) {
                                select.classList.add('text-white');
                            } else {
                                select.classList.remove('text-white');
                            }
                        }

                        document.addEventListener('DOMContentLoaded', () => {
                            document.querySelectorAll('select[name="categoria_id"]').forEach(select => {
                                const initialId = select.getAttribute('data-original');
                                const initialColor = categoriaColores[initialId] || 'light';
                                select.classList.add('bg-' + initialColor);

                                if (['dark', 'primary', 'danger', 'success'].includes(initialColor)) {
                                    select.classList.add('text-white');
                                }
                            });
                        });
                    </script>

                    <script>
                        //  Función común para actualizar el DOM al recibir el evento
                        function actualizarVista(e) {
                            const pacienteId = e.paciente_id;

                            const estadoSelect = document.getElementById(`estado-${pacienteId}`);
                            const categoriaSelect = document.getElementById(`categoria-${pacienteId}`);
                            const feedback = document.getElementById(`feedback-${pacienteId}`);

                            if (estadoSelect) estadoSelect.value = e.estado_id;
                            if (categoriaSelect) {
                                categoriaSelect.value = e.categoria_id;
                                actualizarColor(categoriaSelect);
                            }

                            if (feedback) {
                                feedback.innerHTML = `<span class="text-info">🔄 ${e.estado_nombre} / ${e.categoria_nombre}</span>`;
                                setTimeout(() => {
                                    feedback.innerHTML = '';
                                }, 3000);
                            }
                        }
                    </script>
                    {{-- DataTable --}}
                    <script>
                        $(document).ready(function() {
                            let table = $('#example1').DataTable({
                                pageLength: 10,
                                responsive: true,
                                autoWidth: false,
                                dom: '<"row mb-2"<"col-sm-6"B><"col-sm-6"f>>' + '<"row"<"col-sm-12"tr>>' +
                                    '<"row mt-2"<"col-sm-5"i><"col-sm-7"p>>',
                                language: {
                                    emptyTable: "No hay información",
                                    info: "Mostrando _START_ a _END_ de _TOTAL_ Pacientes",
                                    infoEmpty: "Mostrando 0 a 0 de 0 Pacientes",
                                    infoFiltered: "(Filtrado de _MAX_ total Pacientes)",
                                    lengthMenu: "Mostrar _MENU_ Pacientes",
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
