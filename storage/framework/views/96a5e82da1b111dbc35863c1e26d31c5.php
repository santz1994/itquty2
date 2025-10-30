

<?php $__env->startSection('page_title'); ?>
    Database Authentication Required
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Please confirm your password to access database management tools
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-lock"></i> Security Authentication Required
                </h3>
            </div>
            
            <form method="POST" action="<?php echo e(route('admin.database.process-auth')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="intended" value="<?php echo e($intended); ?>">
                <input type="hidden" name="action" value="<?php echo e($action); ?>">
                
                <div class="box-body">
                    <div class="alert alert-warning">
                        <h4><i class="icon fa fa-warning"></i> Restricted Access</h4>
                        You are attempting to access sensitive database management features. 
                        Please confirm your password to continue.
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <p><strong>Security Notice:</strong></p>
                        <ul class="mb-0">
                            <li>Database modifications are restricted to authorized personnel</li>
                            <li>Only <?php echo e(auth()->user()->email === 'daniel@quty.co.id' ? 'your account' : 'daniel@quty.co.id'); ?> can perform database edits</li>
                            <li>All database operations are logged for security auditing</li>
                            <li>Authentication expires after 30 minutes of inactivity</li>
                        </ul>
                    </div>

                    <?php if(auth()->user()->email !== 'daniel@quty.co.id'): ?>
                        <div class="alert alert-danger">
                            <h4><i class="icon fa fa-ban"></i> Access Denied</h4>
                            Your account (<?php echo e(auth()->user()->email); ?>) is not authorized to perform database modifications.
                            Only daniel@quty.co.id can access database editing features.
                        </div>
                    <?php else: ?>
                        <div class="form-group <?php echo e($errors->has('password') ? 'has-error' : ''); ?>">
                            <label for="password">
                                <i class="fa fa-key"></i> Confirm Your Password
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control" 
                                   placeholder="Enter your current password"
                                   required 
                                   autofocus>
                            <?php if($errors->has('password')): ?>
                                <span class="help-block"><?php echo e($errors->first('password')); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" required> 
                                    I understand that database operations are irreversible and will be logged
                                </label>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if(auth()->user()->email === 'daniel@quty.co.id'): ?>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa fa-unlock"></i> Authenticate & Continue
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo e(route('admin.database.index')); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Database
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                <i class="fa fa-shield"></i> Secured by password authentication
                            </small>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Security Information Panel -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-info-circle"></i> Security Information
                </h3>
            </div>
            <div class="box-body">
                <h4>Access Control Levels:</h4>
                <ul>
                    <li><strong>View Database:</strong> Super Admin role required</li>
                    <li><strong>View Table Data:</strong> Super Admin role required</li>
                    <li><strong>Create/Edit/Delete Records:</strong> daniel@quty.co.id + Password confirmation</li>
                    <li><strong>Backup/Export:</strong> Super Admin role required</li>
                </ul>
                
                <h4>Security Features:</h4>
                <ul>
                    <li>Role-based access control (Super Admin)</li>
                    <li>Email-based authorization (daniel@quty.co.id for edits)</li>
                    <li>Password re-authentication for sensitive operations</li>
                    <li>Session timeout (30 minutes)</li>
                    <li>Complete operation logging</li>
                    <li>CSRF protection on all forms</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    // Focus on password field
    $('#password').focus();
    
    // Auto-submit on Enter
    $('#password').keypress(function(e) {
        if (e.which === 13) {
            $(this).closest('form').submit();
        }
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\database\authenticate.blade.php ENDPATH**/ ?>