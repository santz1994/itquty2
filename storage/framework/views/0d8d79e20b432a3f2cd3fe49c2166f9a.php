
<?php
    $modalId = $modalId ?? 'confirmationModal';
    $title = $title ?? 'Confirm Action';
    $message = $message ?? 'Are you sure you want to perform this action?';
    $confirmText = $confirmText ?? 'Confirm';
    $cancelText = $cancelText ?? 'Cancel';
    $confirmClass = $confirmClass ?? 'btn-danger';
?>

<div class="modal fade" id="<?php echo e($modalId); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?php echo e($title); ?></h4>
            </div>
            <div class="modal-body">
                <p><?php echo e($message); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e($cancelText); ?></button>
                <button type="button" class="btn <?php echo e($confirmClass); ?>" id="<?php echo e($modalId); ?>Confirm"><?php echo e($confirmText); ?></button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    var <?php echo e($modalId); ?>Form = null;
    
    // Store form reference when modal is triggered
    $('[data-toggle="modal"][data-target="#<?php echo e($modalId); ?>"]').on('click', function() {
        <?php echo e($modalId); ?>Form = $(this).closest('form');
    });
    
    // Handle confirmation
    $('#<?php echo e($modalId); ?>Confirm').on('click', function() {
        if (<?php echo e($modalId); ?>Form) {
            <?php echo e($modalId); ?>Form.submit();
        }
        $('#<?php echo e($modalId); ?>').modal('hide');
    });
});
</script>
<?php $__env->stopPush(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\partials\confirmation-modal.blade.php ENDPATH**/ ?>