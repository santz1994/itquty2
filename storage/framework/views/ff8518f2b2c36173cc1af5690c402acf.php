

<?php $__env->startSection('htmlheader_title'); ?>
    Register
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>

<body class="hold-transition register-page" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="register-box" style="width: 420px; margin: 4% auto;">
        <div class="register-logo" style="text-align:center; margin-bottom: 18px;">
            <div style="background: white; border-radius: 12px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); display:inline-block;">
                <h3 style="margin:0; color:#333; font-weight:700;">Create your account</h3>
                <p style="margin:6px 0 0; color:#666; font-size:13px;">Join Quty Assets to manage IT resources</p>
            </div>
        </div>

        <div class="register-box-body" style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.08);">

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Whoops!</strong> Please correct the highlighted errors.
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(url('/register')); ?>" method="post" novalidate>
                <?php echo csrf_field(); ?>

                <div class="form-group <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                    <label for="name">Full name</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="name" type="text" name="name" class="form-control" placeholder="Your full name" value="<?php echo e(old('name')); ?>" required autofocus>
                    </div>
                    <?php if($errors->has('name')): ?>
                        <span class="help-block text-danger"><?php echo e($errors->first('name')); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                    <label for="email">Email address</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                        <input id="email" type="email" name="email" class="form-control" placeholder="you@example.com" value="<?php echo e(old('email')); ?>" required>
                    </div>
                    <?php if($errors->has('email')): ?>
                        <span class="help-block text-danger"><?php echo e($errors->first('email')); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="password" type="password" name="password" class="form-control" placeholder="Create a password" required>
                    </div>
                    <?php if($errors->has('password')): ?>
                        <span class="help-block text-danger"><?php echo e($errors->first('password')); ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm password</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-log-in"></i></span>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Retype password" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="terms" required> I agree to the <a href="<?php echo e(url('/terms')); ?>">terms</a>
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block" style="height:48px; font-weight:600;">Create account</button>
                    </div>
                </div>
            </form>

            <div class="text-center" style="margin-top:12px;">
                <a href="<?php echo e(url('/login')); ?>">I already have a membership</a>
            </div>
        </div>
    </div>

    <?php echo $__env->make('layouts.partials.scripts_auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        // Initialize iCheck if available
        if (typeof $.fn.iCheck === 'function') {
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });
            });
        }

        // Focus the first input on load
        document.addEventListener('DOMContentLoaded', function () {
            var first = document.getElementById('name');
            if (first) first.focus();
        });
    </script>
</body>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\auth\register.blade.php ENDPATH**/ ?>