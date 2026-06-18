<?php $__env->startSection('content'); ?>
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
                    <div class="mb-3">
                        <label for="filtro-categoria" class="form-label">Filtro por Categoria</label>
                        <select id="filtro-categoria" class="form-select" style="width: 200px;">
                            <option value="">Todas</option>
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($categoria->codigo); ?>"> <?php echo e($categoria->codigo); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <?php
                        $pacienteSinCategoria = $pacientes->first(function ($p) use ($pacienteNuevoId) {
                            $ultima = $p->atenciones->sortByDesc('fecha_atencion')->first();
                            $nombreCategoria = optional($ultima->categoria)->nombre;
                            $esNuevoOReactivado = $p->id == $pacienteNuevoId || session()->has('paciente_reactivado');
                            return $esNuevoOReactivado && 
                                (is_null($nombreCategoria) || strtoupper($nombreCategoria) === 'SIN CATEGORIZAR');
                        });
                    ?>

                    <table id="example1" class="table table-striped table-sm display nowrap compact" style="width:100%">
                        <thead style="background-color: #c0c0c0">
                            <tr>
                                <td style="text-align:center">Número</td>
                                <td style="text-align:center">Rut</td>
                                <td style="text-align:center">Nombre</td>
                                <td style="text-align:center">Apellido</td>
                                <td style="text-align:center">Categoría</td>
                                <td style="text-align:center">Texto categoría</td>
                                <td style="text-align:center">Estado</td>
                                <td style="text-align:center">Acciones</td>
                                <td style="text-align:center">Eliminación de Paciente</td>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $pacienteNuevoId = session()->get('paciente_nuevo_id');
                            ?>

                            <?php $__currentLoopData = $pacientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paciente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td style="text-align:center"><?php echo e($loop->iteration); ?></td>
                                    <td style="text-align:center"><?php echo e($paciente->rut); ?></td>
                                    <td style="text-align:center"><?php echo e($paciente->nombre); ?></td>
                                    <td style="text-align:center"><?php echo e($paciente->apellido); ?></td>

                                    
                                    <td style="text-align:center">
                                        <?php
                                            $colorClase = optional($paciente->categoria)
                                                ? 'bg-' . str_replace('bg-', '', $paciente->categoria->color)
                                                : 'bg-light';
                                        ?>
                                        <select name="categoria_id"
                                            class="form-select form-select-sm text-center rounded <?php echo e($colorClase); ?>"
                                            onchange="actualizarEstado(<?php echo e($paciente->id); ?>); actualizarColor(this);"
                                            id="categoria-<?php echo e($paciente->id); ?>"
                                            data-original="<?php echo e($paciente->categoria_id); ?>">
                                            <option value="" <?php echo e(is_null($paciente->categoria_id) ? 'selected' : ''); ?> disabled> - </option>
                                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($categoria->id); ?>"
                                                    <?php echo e($paciente->categoria_id == $categoria->id ? 'selected' : ''); ?>>
                                                    <?php echo e($categoria->codigo); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>

                                    
                                    <td style="display:none; text-align:center;">
                                        <?php echo e(optional($paciente->categoria)->codigo); ?>

                                    </td>

                                    
                                    <td style="text-align:center">
                                        <select name="estado_id"
                                            class="form-select form-select-sm text-center rounded bg-light"
                                            onchange="actualizarEstado(<?php echo e($paciente->id); ?>)"
                                            id="estado-<?php echo e($paciente->id); ?>">
                                            <option value="" <?php echo e(is_null($paciente->estado_id) ? 'selected' : ''); ?> disabled> - </option>
                                            <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estado): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                               <?php if(!str_contains(strtolower($estado->nombre), 'cama')): ?>
                                                    <option value="<?php echo e($estado->id); ?>"
                                                    <?php echo e($paciente->estado_id == $estado->id ? 'selected' : ''); ?>>
                                                    <?php echo e($estado->nombre); ?>

                                                    </option>
                                                <?php endif; ?>
                                                
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </td>

                                    <td style="text-align:center">
                                        <span id="feedback-<?php echo e($paciente->id); ?>"></span>
                                    </td>

                                    
                                    <td style="text-align: center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-danger btn-sm spinner-btn"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal-<?php echo e($paciente->id); ?>"
                                                title="Eliminar registros">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="deleteModal-<?php echo e($paciente->id); ?>" tabindex="-1"
                                    aria-labelledby="deleteModalLabel-<?php echo e($paciente->id); ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="<?php echo e(url('admin/pacientes/' . $paciente->id)); ?>" method="POST"
                                            data-spinner-color="danger">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteModalLabel-<?php echo e($paciente->id); ?>">
                                                        ¿Estás Seguro de Eliminar el Registro?</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Rut</label>
                                                                <input type="text" value="<?php echo e($paciente->rut); ?>"
                                                                    class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label>Nombre y Apellido</label>
                                                                <input type="text"
                                                                    value="<?php echo e($paciente->nombre . ' ' . $paciente->apellido); ?>"
                                                                    class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
                                                    <button type="submit" class="btn btn-danger confirm-delete"> <i class="bi bi-trash"></i>Eliminar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    
                    <?php if($pacienteSinCategoria): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: true,
                                    confirmButtonText: 'Actualizar',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    customClass: {
                                        popup: 'swal2-border-radius'
                                    }
                                });

                                Toast.fire({
                                    icon: 'info',
                                    title: 'Paciente sin categorizar',
                                    text: 'Haga clic en Actualizar para visualizar el panel correctamente.'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            });
                        </script>
                    <?php endif; ?>
                    
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

                            //fetch(`/admin/pacientes/${pacienteId}/update-category`, {
                                fetch(`<?php echo e(url('admin/pacientes')); ?>/${pacienteId}/update-category`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
                                    // tiempo estimado
                                    const horas = Math.floor(data.tiempo_estimado / 60);
                                    const minutos = data.tiempo_estimado % 60;
                                    // Formateo del tiempo estimado min a horas
                                    const tiempoFormateado =
                                        horas < 0 
                                        ? `${horas} hora${horas > 1 ? 's' : ''}${minutos > 0 ? ` ${minutos} min` : ''}`
                                        : `${minutos} min`;
                                    
                                    const mensaje = `✔️ Actualizado (${tiempoFormateado} estimado)`;
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
                        const categoriaColores = {
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                "<?php echo e($categoria->id); ?>": "<?php echo e(str_replace('bg-', '', $categoria->color)); ?>",
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            if (localStorage.getItem('pacienteNuevo') === 'true') {
                                localStorage.removeItem('pacienteNuevo');
                                location.reload(); // fuerza la recarga para que DataTables tenga el nuevo DOM
                            }
                        });
                    </script>

                    
                    <script>
                        $(document).ready(function() {
                            let table = $('#example1').DataTable({
                                pageLength: 10,
                                responsive: true,
                                autoWidth: false,
                                dom: '<"row mb-2"<"col-sm-6"B><"col-sm-6"f>>' +
                                    '<"row"<"col-sm-12"tr>>' +
                                    '<"row mt-2"<"col-sm-5"i><"col-sm-7"p>>',
                                language: {
                                    emptyTable: "No hay información",
                                    info: "Mostrando Inicio a Final de TOTAL Pacientes",
                                    infoEmpty: "Mostrando 0 a 0 de 0 Pacientes",
                                    infoFiltered: "(Filtrado de _MAX_ total Pacientes)",
                                    lengthMenu: "Mostrar MENU Pacientes",
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
                                        text: 'Visor de columnas',
                                        columns: [0, 1, 2, 3, 4, 6, 7, 8]
                                    }
                                ],
                                columnDefs: [{
                                    targets: 5, // índice de la columna "Texto categoría"
                                    visible: false, // oculta la columna
                                    searchable: true, // permite que el filtro funcione
                                    className: 'never', // opcional: para marcarla como no visible
                                    columnsToggle: false //  excluye del botón "colvis"
                                }]

                            });

                            table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

                            // Filtro por categoría (fuera del objeto DataTable)
                            $('#filtro-categoria').on('change', function() {
                                const valor = $(this).val();
                                const columnaTextoCategoria = 5;
                                table.column(columnaTextoCategoria).search(valor).draw();
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let avisoMostrado = false;

        function verificarFlagPacienteNuevo() {
            if (localStorage.getItem('pacienteNuevo') === 'true' && !avisoMostrado) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: {
                        popup: 'swal2-border-radius'
                    }
                });

                Toast.fire({
                    icon: 'info',
                    title: 'Paciente sin categorizar',
                    text: 'Actualice la página para visualizarlo correctamente en el panel.'
                });

                avisoMostrado = true;

                // Elimina el flag para que no se repita
                localStorage.removeItem('pacienteNuevo');
            }
        }

        // Verifica cada segundo si el flag fue activado
        setInterval(verificarFlagPacienteNuevo, 1000);
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const url = "<?php echo e(route('admin.pacientes.ultimaAtencionSinCategorizar')); ?>";
        let lastNotified = localStorage.getItem('ultima_atencion_notificada');

        function checkUltima() {
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                const item = res.data;
                if (item && item.atencion_id) {
                    const idStr = String(item.atencion_id);
                    if (lastNotified !== idStr) {
                        lastNotified = idStr;
                        localStorage.setItem('ultima_atencion_notificada', idStr);

                        // Mostrar Toast notification para paciente sin categorizar
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: true,
                            confirmButtonText: 'Actualizar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            customClass: {
                                popup: 'swal2-border-radius'
                            }
                        });

                        Toast.fire({
                            icon: 'info',
                            title: 'Paciente sin categorizar',
                            text: 'Actualizace la página para visualizar el panel correctamente.'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    }
                }
            }).catch(err => {
                // Silenciar errores de polling para no molestar la UI
                // console.error('Polling ultimaAtencionSinCategorizar failed', err);
            });
        }

        // Ejecutar inmediatamente y luego cada 5 segundos
        checkUltima();
        setInterval(checkUltima, 5000);
    });
</script>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\urgencia_hfbc\resources\views/admin/condition.blade.php ENDPATH**/ ?>