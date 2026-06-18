<?php $__env->startSection('content'); ?>
<div class="container-fluid px-5">
    <h1 class="text-center fs-1 mb-5"></h1>

    
    <div id="panel-urgencia-dinamico">
        <?php echo $__env->make('admin.panel_urgencia_parcial', [
            'hayCriticos' => $hayCriticos,
            'categorias' => $categorias
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function actualizarPanelUrgencia() {
            const panel = document.getElementById("panel-urgencia-dinamico");
            if (!panel) return;

            fetch("<?php echo e(route('admin.panel_urgencia.dinamico')); ?>")
                .then(response => {
                    if (!response.ok) throw new Error("Error al cargar el panel");
                    return response.text();
                })
                .then(html => {
                    panel.innerHTML = html;
                })
                .catch(error => {
                    console.error("Error al actualizar el panel:", error);
                });
        }

        setInterval(actualizarPanelUrgencia, 5000);
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.vista', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\urgencia_hfbc\resources\views/admin/panel_urgencia.blade.php ENDPATH**/ ?>