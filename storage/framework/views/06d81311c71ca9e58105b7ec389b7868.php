


<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
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
            </div>
            
            <?php if(isset($actions)): ?>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <?php echo $actions; ?>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views/components/page-header.blade.php ENDPATH**/ ?>