<?php
    use Illuminate\Support\Str;
?>

    
    <div>
        <h1 class="text-center fs-2 mb-5" style="font-weight: 900;">
           Prueba de Panel 
        </h1>

        <?php if($hayCriticos): ?>
            <div class="alert alert-danger text-center fs-3 py-4 text-uppercase" style="background-color: #E82A2A; color: white; font-weight: 900;">
                PACIENTE EN RIESGO VITAL, SU ESPERA PUEDE VERSE ENLENTECIDA
            </div>
        <?php endif; ?>
    </div>
    

    
    <div class="d-flex justify-content-center gap-4">
        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($categoria['codigo'] !== 'SIN CATEGORIZAR'): ?>
                <?php
                    $ocupacion = $categoria['cupo'] > 0 ? ($categoria['total'] / $categoria['cupo']) * 100 : 0;
                ?>

                <div style="width: 220px;" class="card text-center bg-<?php echo e($categoria['color']); ?> text-dark border-0 shadow-none">
                    <div class="card-body p-4">
                        <strong class="fs-3"><?php echo e($categoria['codigo']); ?></strong><br>
                        <span class="fs-1">
                            <?php echo e($categoria['total']); ?> / <?php echo e($categoria['cupo']); ?>

                        </span>

                        <?php if($ocupacion > 100): ?>
                            <div class="mt-3">
                                <span class="badge bg-danger text-white w-100 py-2 fs-6 text-center rounded">
                                    <strong>SATURADO</strong>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>


        
        <div id="panel-categorias" class="d-flex justify-content-center flex-nowrap gap-4 px-4 py-3">
            
            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if($categoria['codigo'] !== 'SIN CATEGORIZAR'): ?>
                    
                    
                    

                    
                    <div class="card d-flex flex-row shadow" style="width: 22vw; min-width: 300px;"
                        data-categoria="<?php echo e($categoria['codigo']); ?>">
                        
                        <div
                            style="width: 12px; background-color: var(--bs-<?php echo e($categoria['color']); ?>); border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem;">
                        </div>
                        
                        <div class="flex-grow-1 bg-white p-3">
                            
                            <div class="text-center mb-2">
                                <strong class="fs-5 text-dark"><?php echo e($categoria['codigo']); ?></strong>
                            </div>
                            
                            <?php $__currentLoopData = $categoria['estados']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estadoNombre => $estadoData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <?php if(!($categoria['codigo'] === 'ESI 1' && Str::lower(trim($estadoNombre)) === 'en espera de atencion')): ?>
                                    
                                    <?php
                                        $estadoSlug = Str::slug($estadoNombre);
                                        $espera = $estadoData['promedio'];
                                        $umbral = $categoria['umbrales'];
                                        $color =
                                            $espera > $umbral
                                                ? 'danger'
                                                : ($espera > $umbral * 0.7
                                                    ? 'warning'
                                                    : 'success');
                                    ?>
                                
                                <div class="info-box d-flex bg-light text-dark rounded mb-3 shadow"
                                    id="card-<?php echo e(Str::slug($categoria['codigo'])); ?>-<?php echo e($estadoSlug); ?>">

                                    
                                    <div class="d-flex align-items-center">

                                        
                                        <span class="me-3">
                                            <i class="<?php echo e($estadoData['icono']); ?> fa-2x text-<?php echo e($categoria['color']); ?>"></i>
                                        </span>
                                        
                                        <div>
                                            
                                            <div class="fs-5 fw-bold text-uppercase"><?php echo e($estadoNombre); ?></div>
                                            <div class="fs-3 fw-bold"><?php echo e($estadoData['cantidad']); ?> pacientes</div>
                                            
                                            
                                            
                                            <?php if (! (
                                                $categoria['codigo'] === 'ESI 1' ||
                                                Str::lower(trim($estadoNombre)) === 'en atencion' ||
                                                Str::lower(trim($estadoNombre)) === 'en espera de cama'
                                            )): ?>
                                               
                                                <?php
                                                    $horas = floor($espera / 60);
                                                    $minutos = $espera % 60;
                                                ?>

                                                <span class="fs-3 text-dark fw-bold">
                                                    <?php if($horas > 0): ?>
                                                        <?php echo e($horas); ?> hora<?php echo e($horas > 1 ? 's' : ''); ?>

                                                        <?php if($minutos > 0): ?>
                                                            <?php echo e($minutos); ?> min
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php echo e($minutos); ?> min
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>

                                            <div class="progress mt-2" style="height: 12px;">
                                                <div class="progress-bar bg-<?php echo e($categoria['color']); ?>"
                                                    style="width: <?php echo e(min($espera * 2, 100)); ?>%">
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>



<?php /**PATH C:\xampp\htdocs\urgencia_hfbc\resources\views/admin/panel_urgencia_parcial.blade.php ENDPATH**/ ?>