

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Current User Debug Info</h3>
        </div>
        <div class="card-body">
            <?php if(auth()->guard()->check()): ?>
                <dl class="row">
                    <dt class="col-sm-3">User Name</dt>
                    <dd class="col-sm-9"><strong><?php echo e(Auth::user()->name); ?></strong></dd>

                    <dt class="col-sm-3">User ID</dt>
                    <dd class="col-sm-9"><?php echo e(Auth::user()->id); ?></dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9"><?php echo e(Auth::user()->email); ?></dd>

                    <dt class="col-sm-3">Roles</dt>
                    <dd class="col-sm-9">
                        <?php $roles = Auth::user()->getRoleNames()->toArray(); ?>
                        <?php if(count($roles) > 0): ?>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-primary"><?php echo e($role); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="text-muted">No roles assigned</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-3">Is Admin?</dt>
                    <dd class="col-sm-9">
                        <?php if(in_array('admin', $roles) || in_array('super-admin', $roles)): ?>
                            <span class="badge badge-success">YES</span>
                        <?php else: ?>
                            <span class="badge badge-danger">NO</span>
                        <?php endif; ?>
                    </dd>
                </dl>

                <hr>

                <h4>All Users with Admin/Super-Admin Roles:</h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = \App\User::with('roles')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->hasAnyRole(['admin', 'super-admin'])): ?>
                                <tr>
                                    <td><?php echo e($user->id); ?></td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td>
                                        <?php $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge badge-info"><?php echo e($role); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-warning">
                    You are not logged in!
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\debug\current-user.blade.php ENDPATH**/ ?>