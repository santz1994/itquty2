

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            User Management
            <small>Manage system users and their roles</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Users</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php echo e(session('success')); ?>

                </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <?php echo e(session('error')); ?>

                </div>
                <?php endif; ?>

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-users"></i> System Users
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-users')): ?>
                            <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add New User
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($user->id); ?></td>
                                        <td>
                                            <strong><?php echo e($user->name); ?></strong>
                                            <?php if($user->id === auth()->id()): ?>
                                            <span class="label label-info">You</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td>
                                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="label label-<?php echo e($role->name === 'super-admin' ? 'danger' : ($role->name === 'admin' ? 'warning' : ($role->name === 'management' ? 'info' : 'success'))); ?>">
                                                <?php echo e(ucfirst(str_replace('-', ' ', $role->name))); ?>

                                            </span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </td>
                                        <td><?php echo e($user->created_at->format('M d, Y')); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-info btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit-users')): ?>
                                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-warning btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-users')): ?>
                                                <?php if($user->id !== auth()->id()): ?>
                                                <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger btn-xs" 
                                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No users found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($users->hasPages()): ?>
                        <div class="text-center">
                            <?php echo e($users->links()); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Role Summary -->
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
                                    <?php echo e($users->filter(function($user) use ($role) {
                                        return $user->roles->contains('name', $role->name);
                                    })->count()); ?> users
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/users/index.blade.php ENDPATH**/ ?>