
<?php
    $submitText = $submitText ?? 'Save';
    $submitClass = $submitClass ?? 'btn-primary';
    $cancelRoute = $cancelRoute ?? 'javascript:history.back()';
    $showCancel = $showCancel ?? true;
?>

<div class="form-group">
    <div class="col-md-8 col-md-offset-4">
        <button type="submit" class="btn <?php echo e($submitClass); ?>">
            <i class="fa fa-save"></i> <?php echo e($submitText); ?>

        </button>
        
        <?php if($showCancel): ?>
            <?php if(is_string($cancelRoute) && str_starts_with($cancelRoute, 'javascript:')): ?>
                <a href="<?php echo e($cancelRoute); ?>" class="btn btn-default">
                    <i class="fa fa-times"></i> Cancel
                </a>
            <?php else: ?>
                <a href="<?php echo e(route($cancelRoute)); ?>" class="btn btn-default">
                    <i class="fa fa-times"></i> Cancel
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\Project\ITQuty\quty2\resources\views\partials\form-buttons.blade.php ENDPATH**/ ?>