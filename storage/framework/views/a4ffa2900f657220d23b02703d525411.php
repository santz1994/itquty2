

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/custom-tables.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
    <?php echo $__env->make('components.page-header', [
        'title' => 'Audit Logs',
        'subtitle' => 'System activity and change tracking',
        'icon' => 'fa-history',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => url('/home'), 'icon' => 'fa-dashboard'],
            ['label' => 'Audit Logs', 'active' => true]
        ],
        'actions' => '<button type="button" class="btn btn-default" data-toggle="collapse" data-target="#filter-panel">
                        <i class="fa fa-filter"></i> Filters
                      </button>
                      <a href="' . route('audit-logs.export', request()->all()) . '" class="btn btn-success">
                        <i class="fa fa-download"></i> Export CSV
                      </a>'
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                <i class="fa fa-list"></i> Activity History
                            </h3>
                        </div>

                        <!-- Filter Panel -->
                <div id="filter-panel" class="collapse">
                    <div class="box-body">
                        <form method="GET" action="<?php echo e(route('audit-logs.index')); ?>" id="filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>User</label>
                                        <select name="user_id" class="form-control select2">
                                            <option value="">All Users</option>
                                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                                    <?php echo e($user->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Action</label>
                                        <select name="action" class="form-control">
                                            <option value="">All Actions</option>
                                            <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($action); ?>" <?php echo e(request('action') == $action ? 'selected' : ''); ?>>
                                                    <?php echo e(ucfirst($action)); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Model Type</label>
                                        <select name="model_type" class="form-control">
                                            <option value="">All Models</option>
                                            <?php $__currentLoopData = $modelTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modelType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($modelType); ?>" <?php echo e(request('model_type') == $modelType ? 'selected' : ''); ?>>
                                                    <?php echo e($modelType); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Event Type</label>
                                        <select name="event_type" class="form-control">
                                            <option value="">All Types</option>
                                            <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($eventType); ?>" <?php echo e(request('event_type') == $eventType ? 'selected' : ''); ?>>
                                                    <?php echo e(ucfirst($eventType)); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" name="search" class="form-control" placeholder="Description or IP..." value="<?php echo e(request('search')); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Apply Filters
                                            </button>
                                            <a href="<?php echo e(route('audit-logs.index')); ?>" class="btn btn-default">
                                                <i class="fa fa-times"></i> Clear Filters
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Audit Logs Table -->
                <div class="box-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($auditLogs->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="80">ID</th>
                                        <th width="150">Date/Time</th>
                                        <th width="120">User</th>
                                        <th width="100">Action</th>
                                        <th width="120">Model</th>
                                        <th>Description</th>
                                        <th width="100">Event Type</th>
                                        <th width="120">IP Address</th>
                                        <th width="80">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($log->id); ?></td>
                                            <td>
                                                <small><?php echo e($log->created_at->format('Y-m-d')); ?></small><br>
                                                <small class="text-muted"><?php echo e($log->created_at->format('H:i:s')); ?></small>
                                            </td>
                                            <td>
                                                <?php if($log->user): ?>
                                                    <a href="<?php echo e(route('audit-logs.index', ['user_id' => $log->user_id])); ?>" title="View user's logs">
                                                        <?php echo e($log->user->name); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">System</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                    $actionBadgeClass = [
                                                        'create' => 'success',
                                                        'update' => 'info',
                                                        'delete' => 'danger',
                                                        'login' => 'primary',
                                                        'logout' => 'default',
                                                        'failed_login' => 'warning',
                                                    ][$log->action] ?? 'default';
                                                ?>
                                                <span class="label label-<?php echo e($actionBadgeClass); ?>">
                                                    <?php echo e($log->action_name); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php if($log->model_type): ?>
                                                    <span class="badge bg-purple"><?php echo e($log->model_name); ?></span>
                                                    <?php if($log->model_id): ?>
                                                        <small class="text-muted">#<?php echo e($log->model_id); ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo e(\Illuminate\Support\Str::limit($log->description, 80)); ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                    $eventTypeBadgeClass = [
                                                        'model' => 'primary',
                                                        'auth' => 'success',
                                                        'system' => 'warning',
                                                    ][$log->event_type] ?? 'default';
                                                ?>
                                                <span class="label label-<?php echo e($eventTypeBadgeClass); ?>">
                                                    <?php echo e(ucfirst($log->event_type)); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo e($log->ip_address); ?></small>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('audit-logs.show', $log->id)); ?>" class="btn btn-xs btn-info" title="View details">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="text-center">
                            <?php echo e($auditLogs->appends(request()->all())->links()); ?>

                        </div>

                        <!-- Summary -->
                        <div class="text-center text-muted">
                            <small>
                                Showing <?php echo e($auditLogs->firstItem()); ?> to <?php echo e($auditLogs->lastItem()); ?> of <?php echo e($auditLogs->total()); ?> logs
                            </small>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> No audit logs found matching your criteria.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Cleanup Section (Super Admin Only) -->
                <?php if(auth()->user()->hasRole('super-admin')): ?>
                    <div class="box-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#cleanupModal">
                            <i class="fa fa-trash"></i> Cleanup Old Logs
                        </button>
                        <span class="text-muted">
                            <small>Remove audit logs older than specified days (admin only)</small>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('audit-logs.cleanup')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-trash"></i> Cleanup Old Audit Logs</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-warning"></i> This action will permanently delete old audit logs and cannot be undone.
                    </div>
                    <div class="form-group">
                        <label>Keep logs from the last:</label>
                        <select name="days_to_keep" class="form-control" required>
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90" selected>90 days (Recommended)</option>
                            <option value="120">120 days</option>
                            <option value="180">180 days (6 months)</option>
                            <option value="365">365 days (1 year)</option>
                        </select>
                        <small class="help-block">Logs older than this will be permanently deleted.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Delete Old Logs
                    </button>
                </div>
            </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Hide loading overlay when page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(function() {
            hideLoadingOverlay();
        }, 300);
    });
    
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select a user",
        allowClear: true
    });

    // Auto-open filter panel if filters are applied
    <?php if(request()->hasAny(['user_id', 'action', 'model_type', 'event_type', 'start_date', 'end_date', 'search'])): ?>
        $('#filter-panel').addClass('in');
    <?php endif; ?>
    
    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\audit_logs\index.blade.php ENDPATH**/ ?>