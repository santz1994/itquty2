

<?php $__env->startSection('main-content'); ?>
    <section class="content-header">
        <h1>
            Roles Management
            <small>Manage user roles and their permissions</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Roles Overview -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-users"></i> System Roles
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-primary btn-sm" onclick="clearCache()">
                                <i class="fa fa-refresh"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-<?php echo e($role->name === 'super-admin' ? 'red' : ($role->name === 'admin' ? 'yellow' : ($role->name === 'management' ? 'blue' : 'green'))); ?>">
                                        <i class="fa fa-<?php echo e($role->name === 'super-admin' ? 'crown' : ($role->name === 'admin' ? 'user-tie' : ($role->name === 'management' ? 'briefcase' : 'user'))); ?>"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?php echo e(ucfirst(str_replace('-', ' ', $role->name))); ?></span>
                                        <span class="info-box-number">
                                            <?php echo e($role->users_count); ?> users
                                            <small>/ <?php echo e($role->permissions_count); ?> permissions</small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Details -->
        <div class="row">
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-list"></i> Role Permissions
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong><?php echo e(ucfirst(str_replace('-', ' ', $role->name))); ?></strong>
                                <span class="badge pull-right"><?php echo e($role->permissions->count()); ?> permissions</span>
                            </div>
                            <div class="panel-body">
                                <?php if($role->permissions->count() > 0): ?>
                                    <div class="row">
                                        <?php $__currentLoopData = $role->permissions->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6">
                                            <span class="label label-primary"><?php echo e($permission->name); ?></span>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($role->permissions->count() > 6): ?>
                                        <div class="col-md-12">
                                            <small class="text-muted">
                                                ... and <?php echo e($role->permissions->count() - 6); ?> more permissions
                                            </small>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <em class="text-muted">No permissions assigned</em>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user"></i> User Assignments
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="media">
                            <div class="media-left">
                                <span class="media-object">
                                    <i class="fa fa-user-circle fa-2x text-primary"></i>
                                </span>
                            </div>
                            <div class="media-body">
                                <h5 class="media-heading"><?php echo e($user->name); ?></h5>
                                <p class="text-muted"><?php echo e($user->email); ?></p>
                                <div>
                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="label label-<?php echo e($role->name === 'super-admin' ? 'danger' : ($role->name === 'admin' ? 'warning' : ($role->name === 'management' ? 'info' : 'success'))); ?>">
                                        <?php echo e(ucfirst(str_replace('-', ' ', $role->name))); ?>

                                    </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-exclamation-triangle"></i> Role Management Actions
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Role Hierarchy:</strong>
                            <ul>
                                <li><strong>super-admin:</strong> Full system access including settings and user management</li>
                                <li><strong>admin:</strong> Management functions for assets, tickets, and users</li>
                                <li><strong>management:</strong> Reporting and oversight capabilities</li>
                                <li><strong>user:</strong> Basic functionality for assets and tickets</li>
                            </ul>
                        </div>
                        
                        <div class="btn-group" role="group">
                            <a href="<?php echo e(route('system.permissions')); ?>" class="btn btn-primary">
                                <i class="fa fa-key"></i> Manage Permissions
                            </a>
                            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-success">
                                <i class="fa fa-users"></i> Manage Users
                            </a>
                            <button type="button" class="btn btn-warning" onclick="clearCache()">
                                <i class="fa fa-refresh"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function clearCache() {
    if (confirm('Are you sure you want to clear all caches? This may temporarily slow down the application.')) {
        fetch('<?php echo e(route("system.cache.clear")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ Error: ' + error.message);
        });
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/system/roles.blade.php ENDPATH**/ ?>