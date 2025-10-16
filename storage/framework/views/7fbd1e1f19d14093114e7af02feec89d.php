

<?php $__env->startSection('main-content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Create New User
            <small>Add a new user to the system</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo e(route('admin.dashboard')); ?>">Admin</a></li>
            <li><a href="<?php echo e(route('users.index')); ?>">Users</a></li>
            <li class="active">Create</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user-plus"></i> User Information
                        </h3>
                    </div>
                    <form method="POST" action="<?php echo e(route('users.store')); ?>" role="form">
                        <?php echo csrf_field(); ?>
                        <div class="box-body">
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger">
                                    <ul class="list-unstyled">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><i class="fa fa-exclamation-circle"></i> <?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group <?php echo e($errors->has('name') ? 'has-error' : ''); ?>">
                                        <label for="name">Full Name <span class="text-red">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               value="<?php echo e(old('name')); ?>" 
                                               placeholder="Enter full name"
                                               required>
                                        <?php if($errors->has('name')): ?>
                                            <span class="help-block"><?php echo e($errors->first('name')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group <?php echo e($errors->has('email') ? 'has-error' : ''); ?>">
                                        <label for="email">Email Address <span class="text-red">*</span></label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?php echo e(old('email')); ?>" 
                                               placeholder="Enter email address"
                                               required>
                                        <?php if($errors->has('email')): ?>
                                            <span class="help-block"><?php echo e($errors->first('email')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                                        <label for="password">Password <span class="text-red">*</span></label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Enter password"
                                               required>
                                        <?php if($errors->has('password')): ?>
                                            <span class="help-block"><?php echo e($errors->first('password')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group <?php echo e($errors->has('password_confirmation') ? 'has-error' : ''); ?>">
                                        <label for="password_confirmation">Confirm Password <span class="text-red">*</span></label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               placeholder="Confirm password"
                                               required>
                                        <?php if($errors->has('password_confirmation')): ?>
                                            <span class="help-block"><?php echo e($errors->first('password_confirmation')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group <?php echo e($errors->has('phone') ? 'has-error' : ''); ?>">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="phone" 
                                               name="phone" 
                                               value="<?php echo e(old('phone')); ?>" 
                                               placeholder="Enter phone number">
                                        <?php if($errors->has('phone')): ?>
                                            <span class="help-block"><?php echo e($errors->first('phone')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group <?php echo e($errors->has('division_id') ? 'has-error' : ''); ?>">
                                        <label for="division_id">Division</label>
                                        <select class="form-control" id="division_id" name="division_id">
                                            <option value="">Select Division</option>
                                            <?php if(isset($divisions)): ?>
                                                <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($division && is_object($division) && isset($division->name) && isset($division->id)): ?>
                                                        <option value="<?php echo e($division->id); ?>" <?php echo e(old('division_id') == $division->id ? 'selected' : ''); ?>>
                                                            <?php echo e($division->name); ?>

                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php if($errors->has('division_id')): ?>
                                            <span class="help-block"><?php echo e($errors->first('division_id')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group <?php echo e($errors->has('role') ? 'has-error' : ''); ?>">
                                        <label for="role">User Role <span class="text-red">*</span></label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <?php if(isset($roles)): ?>
                                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($role && is_object($role) && isset($role->name)): ?>
                                                        <option value="<?php echo e($role->name); ?>" <?php echo e(old('role') == $role->name ? 'selected' : ''); ?>>
                                                            <?php echo e(ucfirst($role->name)); ?>

                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php if($errors->has('role')): ?>
                                            <span class="help-block"><?php echo e($errors->first('role')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active">Status</label>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" 
                                                       id="is_active" 
                                                       name="is_active" 
                                                       value="1" 
                                                       <?php echo e(old('is_active', 1) ? 'checked' : ''); ?>>
                                                Active User
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="callout callout-info">
                                <h4><i class="fa fa-info-circle"></i> Note:</h4>
                                <p>The new user will receive an email notification with their login credentials. Make sure to provide a valid email address.</p>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create User
                            </button>
                            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Password strength indicator
    $('#password').on('keyup', function() {
        var password = $(this).val();
        var strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        var strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        var strengthColor = ['danger', 'warning', 'info', 'primary', 'success'];
        
        if (password.length > 0) {
            if (!$('#password-strength').length) {
                $('#password').parent().append('<div id="password-strength" class="help-block"></div>');
            }
            $('#password-strength').html('<small class="text-' + strengthColor[strength-1] + '">Password Strength: ' + strengthText[strength-1] + '</small>');
        } else {
            $('#password-strength').remove();
        }
    });
    
    // Confirm password validation
    $('#password_confirmation').on('keyup', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                $(this).parent().removeClass('has-error').addClass('has-success');
                if (!$('#password-match').length) {
                    $(this).parent().append('<div id="password-match" class="help-block"></div>');
                }
                $('#password-match').html('<small class="text-success"><i class="fa fa-check"></i> Passwords match</small>');
            } else {
                $(this).parent().removeClass('has-success').addClass('has-error');
                if (!$('#password-match').length) {
                    $(this).parent().append('<div id="password-match" class="help-block"></div>');
                }
                $('#password-match').html('<small class="text-danger"><i class="fa fa-times"></i> Passwords do not match</small>');
            }
        } else {
            $(this).parent().removeClass('has-error has-success');
            $('#password-match').remove();
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/admin/users/create.blade.php ENDPATH**/ ?>