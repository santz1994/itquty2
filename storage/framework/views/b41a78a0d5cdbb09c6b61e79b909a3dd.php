
<?php
    $size = $size ?? 'sm';
    $showView = $showView ?? true;
    $showEdit = $showEdit ?? true;
    $showDelete = $showDelete ?? true;
    $viewRoute = $viewRoute ?? '';
    $editRoute = $editRoute ?? '';
    $deleteRoute = $deleteRoute ?? '';
    $viewPermission = $viewPermission ?? '';
    $editPermission = $editPermission ?? '';
    $deletePermission = $deletePermission ?? '';
?>

<div class="btn-group">
    <?php if($showView && $viewRoute): ?>
        <?php if(!$viewPermission || auth()->user()->can($viewPermission)): ?>
            <a href="<?php echo e($viewRoute); ?>" class="btn btn-info btn-<?php echo e($size); ?>" title="View">
                <i class="fa fa-eye"></i>
            </a>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if($showEdit && $editRoute): ?>
        <?php if(!$editPermission || auth()->user()->can($editPermission)): ?>
            <a href="<?php echo e($editRoute); ?>" class="btn btn-warning btn-<?php echo e($size); ?>" title="Edit">
                <i class="fa fa-edit"></i>
            </a>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if($showDelete && $deleteRoute): ?>
        <?php if(!$deletePermission || auth()->user()->can($deletePermission)): ?>
            <form method="POST" action="<?php echo e($deleteRoute); ?>" style="display: inline-block;" class="delete-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger btn-<?php echo e($size); ?>" title="Delete" 
                        onclick="return confirm('Are you sure you want to delete this item?')">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if(isset($customActions)): ?>
        <?php echo e($customActions); ?>

    <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        
        var form = this;
        
        swal({
            title: 'Are you sure?',
            text: 'This action cannot be undone!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.value) {
                form.submit();
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\partials\action-buttons.blade.php ENDPATH**/ ?>