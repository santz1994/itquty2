

<?php $__env->startSection('main-content'); ?>
    <section class="content-header">
        <h1>
            User Roles Management
            <small>Manage user roles and permissions</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo e(route('users.index')); ?>">Users</a></li>
            <li class="active">Roles</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-users"></i> User Roles Overview
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-primary btn-sm" onclick="alert('Role management feature to be implemented')">
                                <i class="fa fa-cog"></i> Manage Roles
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if(isset($roles) && count($roles) > 0): ?>
                            <div class="row">
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="box box-widget">
                                        <div class="box-header with-border">
                                            <div class="user-block">
                                                <span class="username">
                                                    <a href="#"><?php echo e(ucfirst($role->name)); ?></a>
                                                    <span class="label label-<?php echo e($role->name == 'super-admin' ? 'danger' : ($role->name == 'admin' ? 'warning' : 'info')); ?> pull-right">
                                                        <?php echo e($role->users->count()); ?> <?php echo e($role->users->count() == 1 ? 'User' : 'Users'); ?>

                                                    </span>
                                                </span>
                                                <span class="description">Role: <?php echo e($role->name); ?></span>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <?php if($role->users->count() > 0): ?>
                                                <h5>Users with this role:</h5>
                                                <ul class="list-unstyled">
                                                    <?php $__currentLoopData = $role->users->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li>
                                                        <i class="fa fa-user text-muted"></i> 
                                                        <?php echo e($user->name); ?>

                                                        <small class="text-muted">(<?php echo e($user->email); ?>)</small>
                                                    </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($role->users->count() > 5): ?>
                                                        <li class="text-muted">
                                                            <i class="fa fa-plus"></i> 
                                                            <?php echo e($role->users->count() - 5); ?> more users...
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted">No users assigned to this role.</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="box-footer">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info" onclick="viewRoleDetails('<?php echo e($role->name); ?>')">
                                                    <i class="fa fa-eye"></i> View Details
                                                </button>
                                                <?php if(auth()->user()->hasRole('super-admin')): ?>
                                                <button type="button" class="btn btn-warning" onclick="editRole('<?php echo e($role->id); ?>')">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="callout callout-info">
                                <h4><i class="fa fa-info-circle"></i> No Roles Found</h4>
                                <p>No user roles are currently defined in the system.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Statistics -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-bar-chart"></i> Role Distribution
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if(isset($roles) && count($roles) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Display Name</th>
                                            <th>Users Count</th>
                                            <th>Permissions</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <span class="label label-<?php echo e($role->name == 'super-admin' ? 'danger' : ($role->name == 'admin' ? 'warning' : 'info')); ?>">
                                                    <?php echo e($role->name); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e(ucwords(str_replace('-', ' ', $role->name))); ?></td>
                                            <td>
                                                <span class="badge bg-blue"><?php echo e($role->users->count()); ?></span>
                                            </td>
                                            <td>
                                                <?php if($role->permissions->count() > 0): ?>
                                                    <span class="badge bg-green"><?php echo e($role->permissions->count()); ?> permissions</span>
                                                <?php else: ?>
                                                    <span class="text-muted">No permissions</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($role->created_at ? $role->created_at->format('Y-m-d') : 'N/A'); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-xs">
                                                    <button class="btn btn-info" onclick="viewRoleUsers(<?php echo e($role->id); ?>)">
                                                        <i class="fa fa-users"></i>
                                                    </button>
                                                    <?php if(auth()->user()->hasRole('super-admin')): ?>
                                                    <button class="btn btn-warning" onclick="alert('Role editing feature to be implemented')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Role Details Modal -->
<div class="modal fade" id="roleDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Role Details</h4>
            </div>
            <div class="modal-body" id="roleDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewRoleDetails(roleName) {
    $('#roleDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#roleDetailsModal').modal('show');
    
    // Simulate loading role details
    setTimeout(function() {
        var content = '<h5>Role: ' + roleName.charAt(0).toUpperCase() + roleName.slice(1) + '</h5>';
        content += '<p>This would show detailed information about the role, including permissions and capabilities.</p>';
        content += '<p><em>Feature to be implemented</em></p>';
        
        $('#roleDetailsContent').html(content);
    }, 1000);
}

function viewRoleUsers(roleId) {
    alert('View users for role ID: ' + roleId + '\n(Feature to be implemented)');
}

function editRole(roleId) {
    alert('Edit role ID: ' + roleId + '\n(Feature to be implemented)');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/users/roles.blade.php ENDPATH**/ ?>