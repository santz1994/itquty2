

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', [
    'title' => 'User Management',
    'subtitle' => 'Manage system users and their roles',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Users']
    ],
    'actions' => auth()->user()->can('create-users') ? 
        '<a href="'.route('users.create').'" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New User
        </a>' : ''
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible auto-dismiss">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-check-circle"></i> <?php echo e(session('success')); ?>

                </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible auto-dismiss">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

                </div>
                <?php endif; ?>

                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table-responsive">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-users')): ?>
                            <div class="table-toolbar" style="margin-bottom:10px;">
                                <button id="bulk-delete-btn" class="btn btn-danger" disabled>
                                    <i class="fa fa-trash"></i> Delete Selected
                                </button>
                            </div>
                            <?php endif; ?>
                            <table class="table table-enhanced table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:40px; text-align:center;"> 
                                            <input type="checkbox" id="select-all" title="Select all">
                                        </th>
                                        <th class="sortable" data-column="id">ID</th>
                                        <th class="sortable" data-column="name">Name</th>
                                        <th class="sortable" data-column="email">Email</th>
                                        <th>Roles</th>
                                        <th class="sortable" data-column="created_at">Created</th>
                                        <th class="actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td style="text-align:center; vertical-align: middle;">
                                            <input type="checkbox" class="row-check" data-id="<?php echo e($user->id); ?>">
                                        </td>
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
                                        <td class="table-actions" style="white-space: nowrap;">
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('users.show', $user)); ?>" 
                                                   class="btn btn-info btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="View Details">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <a href="<?php echo e(route('users.edit', $user)); ?>" 
                                                   class="btn btn-warning btn-sm"
                                                   data-toggle="tooltip" 
                                                   title="Edit User">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <?php if($user->id !== auth()->id()): ?>
                                                <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" style="display: inline;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm delete-confirm" 
                                                            data-item-name="user <?php echo e($user->name); ?>"
                                                            data-toggle="tooltip" 
                                                            title="Delete User">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                <?php else: ?>
                                                <button type="button" 
                                                        class="btn btn-secondary btn-sm" 
                                                        disabled
                                                        data-toggle="tooltip" 
                                                        title="Cannot delete yourself">
                                                    <i class="fa fa-ban"></i> Cannot Delete
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class="fa fa-users"></i>
                                                </div>
                                                <div class="empty-state-title">No Users Found</div>
                                                <div class="empty-state-description">
                                                    There are no users matching your search criteria.
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-users')): ?>
                                                    <br>Try adjusting your filters or create a new user.
                                                    <?php endif; ?>
                                                </div>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create-users')): ?>
                                                <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                                                    <i class="fa fa-plus"></i> Add New User
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
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

<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Delete confirmation
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var itemName = $(this).data('item-name') || 'this user';
        
        if (confirm('Are you sure you want to delete ' + itemName + '? This action cannot be undone.')) {
            showLoading('Deleting user...');
            form.submit();
        }
    });
    
    // Loading state for action buttons
    $('.btn-group a').on('click', function() {
        var action = $(this).attr('title') || 'Processing';
        showLoading(action + '...');
    });

    // Select all / row selection handling
    $('#select-all').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    $('.row-check').on('change', function() {
        var anyChecked = $('.row-check:checked').length > 0;
        $('#bulk-delete-btn').prop('disabled', !anyChecked);
    });

    // Bulk delete handler
    $('#bulk-delete-btn').on('click', function(e) {
        e.preventDefault();
        var ids = $('.row-check:checked').map(function() { return $(this).data('id'); }).get();
        if (!ids.length) return;

        if (!confirm('Are you sure you want to delete the selected users? This action cannot be undone.')) return;

        showLoading('Deleting selected users...');

        var token = '<?php echo e(csrf_token()); ?>';

        fetch('<?php echo e(route('users.bulk-delete')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ids: ids })
        }).then(function(resp) {
            if (resp.ok) return resp.json();
            throw new Error('Failed to delete');
        }).then(function(json) {
            hideLoading();
            if (json.success) {
                // Remove deleted rows from table
                ids.forEach(function(id) { $('.row-check[data-id="' + id + '"]').closest('tr').remove(); });
                $('#bulk-delete-btn').prop('disabled', true);
                showFlash('Users deleted successfully', 'success');
            } else {
                showFlash(json.message || 'Failed to delete users', 'error');
            }
        }).catch(function(err) {
            hideLoading();
            console.error(err);
            showFlash('An error occurred while deleting users', 'error');
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\users\index.blade.php ENDPATH**/ ?>