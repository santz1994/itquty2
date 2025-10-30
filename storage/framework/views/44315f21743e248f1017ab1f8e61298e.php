

<?php $__env->startSection('main-content'); ?>
    <section class="content-header">
        <h1>
            Permissions Management
            <small>Manage system permissions and role assignments</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo e(route('system.settings')); ?>">System</a></li>
            <li class="active">Permissions</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Permissions List -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-key"></i> System Permissions
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if(isset($permissions) && count($permissions) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Permission Name</th>
                                        <th>Guard</th>
                                        <th>Assigned Roles</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong><?php echo e($permission->name); ?></strong></td>
                                        <td><?php echo e($permission->guard_name); ?></td>
                                        <td>
                                            <?php if($permission->roles->count() > 0): ?>
                                                <?php $__currentLoopData = $permission->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="label label-<?php echo e($role->name === 'super-admin' ? 'danger' : ($role->name === 'admin' ? 'warning' : 'info')); ?>">
                                                    <?php echo e($role->name); ?>

                                                </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <span class="text-muted">No roles assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($permission->created_at->format('M d, Y')); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-xs">
                                                <button class="btn btn-info" onclick="editPermission(<?php echo e($permission->id); ?>)">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <?php if($permission->roles->count() === 0): ?>
                                                <button class="btn btn-danger" onclick="deletePermission(<?php echo e($permission->id); ?>)">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No permissions found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Permission Actions -->
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-plus"></i> Create Permission
                        </h3>
                    </div>
                    <div class="box-body">
                        <form method="POST" action="<?php echo e(route('system.permissions.create')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label for="permission_name">Permission Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="permission_name" 
                                       name="name" 
                                       placeholder="e.g., view-reports" 
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="guard_name">Guard</label>
                                <select class="form-control" id="guard_name" name="guard_name">
                                    <option value="web">Web</option>
                                    <option value="api">API</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-plus"></i> Create Permission
                            </button>
                        </form>
                    </div>
                </div>

                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-link"></i> Assign to Role
                        </h3>
                    </div>
                    <div class="box-body">
                        <form method="POST" action="<?php echo e(route('system.permissions.assign')); ?>">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label for="permission_id">Permission</label>
                                <select class="form-control" id="permission_id" name="permission_id" required>
                                    <option value="">Select Permission...</option>
                                    <?php if(isset($permissions)): ?>
                                        <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($permission->id); ?>"><?php echo e($permission->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    <option value="">Select Role...</option>
                                    <?php if(isset($roles)): ?>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($role->id); ?>"><?php echo e(ucfirst($role->name)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fa fa-link"></i> Assign Permission
                            </button>
                        </form>
                    </div>
                </div>

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> Permission Info
                        </h3>
                    </div>
                    <div class="box-body">
                        <p><strong>Total Permissions:</strong> <?php echo e(isset($permissions) ? count($permissions) : 0); ?></p>
                        <p><strong>Total Roles:</strong> <?php echo e(isset($roles) ? count($roles) : 0); ?></p>
                        <p><strong>Unassigned Permissions:</strong> 
                            <?php echo e(isset($permissions) ? $permissions->filter(function($p) { return $p->roles->count() === 0; })->count() : 0); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-Permission Matrix -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-table"></i> Permission Matrix
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if(isset($roles) && isset($permissions) && count($roles) > 0 && count($permissions) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>Permission</th>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="text-center"><?php echo e(ucfirst($role->name)); ?></th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong><?php echo e($permission->name); ?></strong></td>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="text-center">
                                            <?php if($role->hasPermissionTo($permission->name)): ?>
                                                <i class="fa fa-check text-success"></i>
                                            <?php else: ?>
                                                <i class="fa fa-times text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No data available for permission matrix.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function editPermission(permissionId) {
    // Implement permission editing
    alert('Edit permission ID: ' + permissionId + ' (Feature to be implemented)');
}

function deletePermission(permissionId) {
    if (confirm('Are you sure you want to delete this permission? This action cannot be undone.')) {
        // Implement permission deletion
        alert('Delete permission ID: ' + permissionId + ' (Feature to be implemented)');
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\system\permissions.blade.php ENDPATH**/ ?>