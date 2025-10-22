

<?php $__env->startSection('htmlheader_title'); ?>
    Insufficient Permissions
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentheader_title'); ?>
    403 Error
<?php $__env->stopSection(); ?>

<?php $__env->startSection('$contentheader_description'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>

<div class="error-page">
    <h2 class="headline text-yellow"> 403</h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> Oops! Insufficient Permissions.</h3>
        <p>
            You do not have the required permission to view this page.
        </p
    </div><!-- /.error-content -->
</div><!-- /.error-page -->
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/errors/403.blade.php ENDPATH**/ ?>