

<?php $__env->startSection('htmlheader_title'); ?>
    Password recovery
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>

<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="<?php echo e(url('/home')); ?>"><b>Quty</b>Assets</a>
        </div><!-- /.login-logo -->

        <div class="login-box-body">
            <h4 class="text-center" style="margin-bottom:18px;">Reset your password</h4>

            
            <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Whoops!</strong> There were some problems with your input.
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <p class="text-muted text-center">Enter the email associated with your account and we'll send a password reset link.</p>

            <form action="<?php echo e(url('/password/email')); ?>" method="post" novalidate>
                <?php echo csrf_field(); ?>

                <div class="form-group <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                    <label for="email" class="control-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                        <input id="email" type="email" name="email" class="form-control" placeholder="you@example.com" value="<?php echo e(old('email')); ?>" required autofocus aria-describedby="emailHelp">
                    </div>
                    <small id="emailHelp" class="form-text text-muted">We'll email you a link to reset your password.</small>
                    <?php if($errors->has('email')): ?>
                        <span class="help-block text-danger"><?php echo e($errors->first('email')); ?></span>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Send Password Reset Link</button>
                    </div>
                </div>
            </form>

            <div class="text-center" style="margin-top:12px;">
                <a href="<?php echo e(url('/login')); ?>">Back to login</a>
            </div>

        </div><!-- /.login-box-body -->

    </div><!-- /.login-box -->

    <?php echo $__env->make('layouts.partials.scripts_auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        // Initialize iCheck if present
        if (typeof $.fn.iCheck === 'function') {
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });
            });
        }

        // Minimal client-side enhancement: focus email on load
        document.addEventListener('DOMContentLoaded', function () {
            var email = document.getElementById('email');
            if (email) email.focus();
        });
    </script>
</body>
<footer>
    <div class="container">
        <p class="text-center" style="margin: 2px; padding: 20px 0; color: #fff9f9;">
            &copy; <?php echo e(date('Y')); ?> Quty Assets. All rights reserved.
        </p>
    </div>
</footer>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\auth\passwords\email.blade.php ENDPATH**/ ?>