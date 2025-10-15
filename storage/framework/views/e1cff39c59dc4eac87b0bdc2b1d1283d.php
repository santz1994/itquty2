

<?php $__env->startSection('main-content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            System Logs
            <small>View and manage application logs</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo e(route('system.settings')); ?>">System</a></li>
            <li class="active">Logs</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Log Viewer -->
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-file-text"></i> Log Entries
                        </h3>
                        <div class="box-tools pull-right">
                            <form method="GET" action="<?php echo e(route('system.logs')); ?>" style="display: inline;">
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <input type="text" name="search" class="form-control" placeholder="Search logs..." value="<?php echo e(request('search')); ?>">
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 600px; overflow-y: auto;">
                        <?php if(isset($logs) && count($logs) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Level</th>
                                        <th>Message</th>
                                        <th>Context</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e($log['level'] === 'error' ? 'danger' : ($log['level'] === 'warning' ? 'warning' : '')); ?>">
                                        <td><?php echo e($log['timestamp'] ?? 'Unknown'); ?></td>
                                        <td>
                                            <span class="label label-<?php echo e($log['level'] === 'error' ? 'danger' : ($log['level'] === 'warning' ? 'warning' : ($log['level'] === 'info' ? 'info' : 'default'))); ?>">
                                                <?php echo e(strtoupper($log['level'] ?? 'UNKNOWN')); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($log['message'] ?? 'No message'); ?></td>
                                        <td>
                                            <?php if(isset($log['context']) && !empty($log['context'])): ?>
                                            <button class="btn btn-xs btn-info" onclick="showContext('<?php echo e($log['id'] ?? 'unknown'); ?>')">
                                                <i class="fa fa-info-circle"></i> View
                                            </button>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center">
                            <i class="fa fa-file-text-o fa-3x text-muted"></i>
                            <p class="text-muted">No log entries found.</p>
                            <?php if(request('search')): ?>
                            <p>Try a different search term or <a href="<?php echo e(route('system.logs')); ?>">view all logs</a>.</p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if(isset($logs) && count($logs) > 0): ?>
                    <div class="box-footer">
                        <small class="text-muted">Showing <?php echo e(count($logs)); ?> log entries</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Log Controls -->
            <div class="col-md-3">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-cogs"></i> Log Controls
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label>Filter by Level:</label>
                            <form method="GET" action="<?php echo e(route('system.logs')); ?>">
                                <select class="form-control" name="level" onchange="this.form.submit()">
                                    <option value="">All Levels</option>
                                    <option value="emergency" <?php echo e(request('level') === 'emergency' ? 'selected' : ''); ?>>Emergency</option>
                                    <option value="alert" <?php echo e(request('level') === 'alert' ? 'selected' : ''); ?>>Alert</option>
                                    <option value="critical" <?php echo e(request('level') === 'critical' ? 'selected' : ''); ?>>Critical</option>
                                    <option value="error" <?php echo e(request('level') === 'error' ? 'selected' : ''); ?>>Error</option>
                                    <option value="warning" <?php echo e(request('level') === 'warning' ? 'selected' : ''); ?>>Warning</option>
                                    <option value="notice" <?php echo e(request('level') === 'notice' ? 'selected' : ''); ?>>Notice</option>
                                    <option value="info" <?php echo e(request('level') === 'info' ? 'selected' : ''); ?>>Info</option>
                                    <option value="debug" <?php echo e(request('level') === 'debug' ? 'selected' : ''); ?>>Debug</option>
                                </select>
                                <?php if(request('search')): ?>
                                <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                                <?php endif; ?>
                            </form>
                        </div>

                        <div class="form-group">
                            <label>Date Range:</label>
                            <form method="GET" action="<?php echo e(route('system.logs')); ?>">
                                <select class="form-control" name="date" onchange="this.form.submit()">
                                    <option value="">All Dates</option>
                                    <option value="today" <?php echo e(request('date') === 'today' ? 'selected' : ''); ?>>Today</option>
                                    <option value="yesterday" <?php echo e(request('date') === 'yesterday' ? 'selected' : ''); ?>>Yesterday</option>
                                    <option value="week" <?php echo e(request('date') === 'week' ? 'selected' : ''); ?>>This Week</option>
                                    <option value="month" <?php echo e(request('date') === 'month' ? 'selected' : ''); ?>>This Month</option>
                                </select>
                                <?php if(request('search')): ?>
                                <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                                <?php endif; ?>
                                <?php if(request('level')): ?>
                                <input type="hidden" name="level" value="<?php echo e(request('level')); ?>">
                                <?php endif; ?>
                            </form>
                        </div>

                        <hr>

                        <form method="POST" action="<?php echo e(route('system.logs.clear')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to clear all logs? This action cannot be undone.')">
                                <i class="fa fa-trash"></i> Clear All Logs
                            </button>
                        </form>

                        <a href="<?php echo e(route('system.logs.download')); ?>" class="btn btn-info btn-block">
                            <i class="fa fa-download"></i> Download Logs
                        </a>
                    </div>
                </div>

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> Log Statistics
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>Total Entries:</strong></td>
                                <td><?php echo e($stats['total'] ?? 0); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Errors:</strong></td>
                                <td><span class="text-red"><?php echo e($stats['errors'] ?? 0); ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Warnings:</strong></td>
                                <td><span class="text-yellow"><?php echo e($stats['warnings'] ?? 0); ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Info:</strong></td>
                                <td><span class="text-blue"><?php echo e($stats['info'] ?? 0); ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Log Size:</strong></td>
                                <td><?php echo e($stats['file_size'] ?? 'Unknown'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Last Entry:</strong></td>
                                <td><?php echo e($stats['last_entry'] ?? 'Never'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-file"></i> Log Files
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if(isset($log_files) && count($log_files) > 0): ?>
                        <ul class="list-unstyled">
                            <?php $__currentLoopData = $log_files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e(route('system.logs', ['file' => $file['name']])); ?>" 
                                   class="text-<?php echo e($file['name'] === request('file', 'laravel.log') ? 'primary' : 'default'); ?>">
                                    <i class="fa fa-file-text-o"></i> <?php echo e($file['name']); ?>

                                </a>
                                <br>
                                <small class="text-muted"><?php echo e($file['size']); ?> - <?php echo e($file['modified']); ?></small>
                            </li>
                            <?php if(!$loop->last): ?><hr style="margin: 10px 0;"><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-muted">No log files found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function showContext(logId) {
    // Implement context viewing
    alert('Show context for log ID: ' + logId + ' (Feature to be implemented)');
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/system/logs.blade.php ENDPATH**/ ?>