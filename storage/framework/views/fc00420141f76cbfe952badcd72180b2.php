

<?php $__env->startSection('main-content'); ?>
    <section class="content-header">
        <h1>
            System Maintenance
            <small>System maintenance and cleanup tools</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo e(route('admin.dashboard')); ?>">Admin</a></li>
            <li class="active">Maintenance</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Cache Management -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-refresh"></i> Cache Management
                        </h3>
                    </div>
                    <div class="box-body">
                        <p>Clear application cache to improve performance and apply new configurations.</p>
                        <div class="form-inline">
                            <div class="form-group">
                                <select id="cache_type" class="form-control" required>
                                    <option value="">Select cache type...</option>
                                    <option value="all">All Caches</option>
                                    <option value="application">Application Cache</option>
                                    <option value="config">Config Cache</option>
                                    <option value="route">Route Cache</option>
                                    <option value="view">View Cache</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="clearCache()">
                                <i class="fa fa-trash"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Cleanup -->
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-hdd-o"></i> Storage Cleanup
                        </h3>
                    </div>
                    <div class="box-body">
                        <p>Clean up temporary files and logs to free up disk space.</p>
                        <div class="btn-group-vertical btn-block">
                            <button type="button" class="btn btn-warning btn-block" onclick="clearLogs()">
                                <i class="fa fa-file-text"></i> Clear Log Files
                            </button>
                            <button type="button" class="btn btn-warning btn-block" onclick="clearTemp()">
                                <i class="fa fa-folder"></i> Clear Temp Files
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Maintenance -->
        <div class="row">
            <div class="col-md-8">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-database"></i> Database Maintenance
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Optimize Tables</h4>
                                <p>Optimize database tables for better performance.</p>
                                <button type="button" class="btn btn-info" onclick="optimizeDB()">
                                    <i class="fa fa-wrench"></i> Optimize DB
                                </button>
                            </div>
                            <div class="col-md-6">
                                <h4>Migration Status</h4>
                                <p>Check and run pending migrations.</p>
                                <button type="button" class="btn btn-info" onclick="runMigrations()">
                                    <i class="fa fa-code-fork"></i> Run Migrations
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> System Status
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>PHP Version:</strong></td>
                                <td><?php echo e(PHP_VERSION); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Laravel:</strong></td>
                                <td><?php echo e(app()->version()); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Environment:</strong></td>
                                <td>
                                    <span class="label label-<?php echo e(app()->environment('production') ? 'danger' : 'success'); ?>">
                                        <?php echo e(strtoupper(app()->environment())); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Debug Mode:</strong></td>
                                <td>
                                    <span class="label label-<?php echo e(config('app.debug') ? 'warning' : 'success'); ?>">
                                        <?php echo e(config('app.debug') ? 'ON' : 'OFF'); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Disk Space:</strong></td>
                                <td><?php echo e(isset($disk_space) ? $disk_space : 'Unknown'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Queue Management -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-tasks"></i> Queue Management
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <button type="button" class="btn btn-default btn-block" onclick="restartQueue()">
                                    <i class="fa fa-refresh"></i> Restart Queue
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-warning btn-block" onclick="clearQueue()">
                                    <i class="fa fa-trash"></i> Clear Queue
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-danger btn-block" onclick="clearFailed()">
                                    <i class="fa fa-exclamation-triangle"></i> Clear Failed
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-info btn-block" onclick="viewQueueStatus()">
                                    <i class="fa fa-eye"></i> View Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Log -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-history"></i> Recent Maintenance Activities
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if(isset($recent_activities) && count($recent_activities) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>User</th>
                                            <th>Status</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $recent_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($activity->created_at->format('Y-m-d H:i:s')); ?></td>
                                            <td><?php echo e($activity->action); ?></td>
                                            <td><?php echo e($activity->user->name ?? 'System'); ?></td>
                                            <td>
                                                <span class="label label-<?php echo e($activity->status == 'success' ? 'success' : 'danger'); ?>">
                                                    <?php echo e(ucfirst($activity->status)); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($activity->details ?? 'N/A'); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No recent maintenance activities recorded.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function clearCache() {
    var cacheType = document.getElementById('cache_type').value;
    if (!cacheType) {
        alert('Please select a cache type first.');
        return;
    }
    
    if (confirm('Clear ' + cacheType + ' cache?')) {
        alert('Cache clearing: ' + cacheType + ' (Feature to be implemented)');
    }
}

function clearLogs() {
    if (confirm('Clear all log files?')) {
        alert('Log files clearing (Feature to be implemented)');
    }
}

function clearTemp() {
    if (confirm('Clear temporary files?')) {
        alert('Temp files clearing (Feature to be implemented)');
    }
}

function optimizeDB() {
    if (confirm('Optimize all database tables?')) {
        alert('Database optimization (Feature to be implemented)');
    }
}

function runMigrations() {
    if (confirm('Run pending migrations?')) {
        alert('Migration running (Feature to be implemented)');
    }
}

function restartQueue() {
    if (confirm('Restart queue workers?')) {
        alert('Queue restart (Feature to be implemented)');
    }
}

function clearQueue() {
    if (confirm('Clear all queued jobs?')) {
        alert('Queue clearing (Feature to be implemented)');
    }
}

function clearFailed() {
    if (confirm('Clear failed jobs?')) {
        alert('Failed jobs clearing (Feature to be implemented)');
    }
}

function viewQueueStatus() {
    alert('Queue status viewing (Feature to be implemented)');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\system\maintenance.blade.php ENDPATH**/ ?>