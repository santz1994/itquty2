

<div class="content-header">
    <div class="container-fluid">
        
        <?php
            $actions_position = $actions_position ?? 'column';
        ?>

        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <?php if(isset($actions) && $actions_position === 'inline'): ?>
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="m-0"><?php echo e($title); ?></h1>
                            <?php if(isset($subtitle)): ?>
                                <p class="text-muted mb-0"><?php echo e($subtitle); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="ml-3 page-header-actions">
                            <?php echo $actions; ?>

                        </div>
                    </div>
                    <?php if(isset($breadcrumbs) && count($breadcrumbs) > 0): ?>
                        <ol class="breadcrumb mt-2">
                            <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="breadcrumb-item <?php echo e(isset($crumb['url']) ? '' : 'active'); ?>">
                                    <?php if(isset($crumb['url'])): ?>
                                        <a href="<?php echo e($crumb['url']); ?>">
                                            <?php if(isset($crumb['icon'])): ?>
                                                <i class="fa fa-<?php echo e($crumb['icon']); ?>"></i>
                                            <?php endif; ?>
                                            <?php echo e($crumb['label']); ?>

                                        </a>
                                    <?php else: ?>
                                        <?php if(isset($crumb['icon'])): ?>
                                            <i class="fa fa-<?php echo e($crumb['icon']); ?>"></i>
                                        <?php endif; ?>
                                        <?php echo e($crumb['label']); ?>

                                    <?php endif; ?>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ol>
                    <?php endif; ?>
                <?php else: ?>
                    <h1 class="m-0"><?php echo e($title); ?></h1>
                    <?php if(isset($subtitle)): ?>
                        <p class="text-muted"><?php echo e($subtitle); ?></p>
                    <?php endif; ?>

                    <?php if(isset($breadcrumbs) && count($breadcrumbs) > 0): ?>
                        <ol class="breadcrumb mt-2">
                            <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="breadcrumb-item <?php echo e(isset($crumb['url']) ? '' : 'active'); ?>">
                                    <?php if(isset($crumb['url'])): ?>
                                        <a href="<?php echo e($crumb['url']); ?>">
                                            <?php if(isset($crumb['icon'])): ?>
                                                <i class="fa fa-<?php echo e($crumb['icon']); ?>"></i>
                                            <?php endif; ?>
                                            <?php echo e($crumb['label']); ?>

                                        </a>
                                    <?php else: ?>
                                        <?php if(isset($crumb['icon'])): ?>
                                            <i class="fa fa-<?php echo e($crumb['icon']); ?>"></i>
                                        <?php endif; ?>
                                        <?php echo e($crumb['label']); ?>

                                    <?php endif; ?>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ol>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if(isset($actions) && $actions_position !== 'inline'): ?>
                <div class="col-sm-6">
                    <div class="float-sm-right page-header-actions">
                        <?php echo $actions; ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views/components/page-header.blade.php ENDPATH**/ ?>