

<?php $__env->startSection('htmlheader_title'); ?>
    Page not found
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentheader_title'); ?>
    404 Error Page
<?php $__env->stopSection(); ?>

<?php $__env->startSection('$contentheader_description'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>

<div class="error-page">
    <h2 class="headline text-yellow"> 404</h2>
    <div class="error-content">
        <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
        <p>
            We could not find the page you were looking for.
            Meanwhile, you may <a href='<?php echo e(url('/home')); ?>'>return to dashboard</a> or try using the search form.
        </p>
        <form class='search-form'>
            <div class='input-group'>
                <input type="text" name="search" class='form-control' placeholder="Search"/>
                <div class="input-group-btn">
                    <button type="submit" name="submit" class="btn btn-warning btn-flat"><i class="fa fa-search"></i></button>
                </div>
            </div><!-- /.input-group -->
        </form>
    </div><!-- /.error-content -->
</div><!-- /.error-page -->
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/errors/404.blade.php ENDPATH**/ ?>