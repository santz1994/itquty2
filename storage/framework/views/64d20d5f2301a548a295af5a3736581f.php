
<?php if(session('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.success('<?php echo e(session('success')); ?>');
        });
    </script>
<?php endif; ?>

<?php if(session('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.error('<?php echo e(session('error')); ?>');
        });
    </script>
<?php endif; ?>

<?php if(session('warning')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.warning('<?php echo e(session('warning')); ?>');
        });
    </script>
<?php endif; ?>

<?php if(session('info')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.info('<?php echo e(session('info')); ?>');
        });
    </script>
<?php endif; ?>


<?php if(session('status') && session('message')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(session('status') == 'success'): ?>
                toastr.success('<?php echo e(session('message')); ?>' + (<?php echo e(session('title') ? "' - ' + '" . session('title') . "'" : "''"); ?>));
            <?php elseif(session('status') == 'error'): ?>
                toastr.error('<?php echo e(session('message')); ?>' + (<?php echo e(session('title') ? "' - ' + '" . session('title') . "'" : "''"); ?>));
            <?php elseif(session('status') == 'warning'): ?>
                toastr.warning('<?php echo e(session('message')); ?>' + (<?php echo e(session('title') ? "' - ' + '" . session('title') . "'" : "''"); ?>));
            <?php else: ?>
                toastr.info('<?php echo e(session('message')); ?>' + (<?php echo e(session('title') ? "' - ' + '" . session('title') . "'" : "''"); ?>));
            <?php endif; ?>
        });
    </script>
<?php endif; ?>


<?php if($errors->any()): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                toastr.error('<?php echo e($error); ?>');
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        });
    </script>
<?php endif; ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Toastr configuration
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Helper functions for manual toastr calls
window.showSuccess = function(message) {
    toastr.success(message);
};

window.showError = function(message) {
    toastr.error(message);
};

window.showWarning = function(message) {
    toastr.warning(message);
};

window.showInfo = function(message) {
    toastr.info(message);
};
</script>
<?php $__env->stopPush(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/partials/toastr-notifications.blade.php ENDPATH**/ ?>