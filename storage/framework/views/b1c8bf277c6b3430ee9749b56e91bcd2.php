

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/dashboard-widgets.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
    <?php echo $__env->make('components.page-header', [
        'title' => 'Admin Dashboard',
        'subtitle' => 'System overview and quick actions',
        'icon' => 'fa-tachometer',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => url('/home'), 'icon' => 'fa-dashboard'],
            ['label' => 'Admin Dashboard', 'active' => true]
        ]
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="content">
        <!-- Modern KPI Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('users.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-primary">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($stats['total_users'] ?? 0); ?></h3>
                            <p class="kpi-label">Total Users</p>
                            <?php if(isset($stats['users_growth']) && $stats['users_growth'] > 0): ?>
                            <span class="kpi-trend positive">
                                <i class="fa fa-arrow-up"></i> <?php echo e($stats['users_growth']); ?>% this month
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('assets.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-danger">
                            <i class="fa fa-desktop"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($stats['total_assets'] ?? 0); ?></h3>
                            <p class="kpi-label">Total Assets</p>
                            <?php if(isset($stats['assets_growth']) && $stats['assets_growth'] > 0): ?>
                            <span class="kpi-trend positive">
                                <i class="fa fa-arrow-up"></i> <?php echo e($stats['assets_growth']); ?>% this month
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('tickets.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-success">
                            <i class="fa fa-ticket"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($stats['active_tickets'] ?? 0); ?></h3>
                            <p class="kpi-label">Active Tickets</p>
                            <?php if(isset($stats['tickets_trend']) && $stats['tickets_trend'] < 0): ?>
                            <span class="kpi-trend positive">
                                <i class="fa fa-arrow-down"></i> <?php echo e(abs($stats['tickets_trend'])); ?>% from last week
                            </span>
                            <?php elseif(isset($stats['tickets_trend']) && $stats['tickets_trend'] > 0): ?>
                            <span class="kpi-trend negative">
                                <i class="fa fa-arrow-up"></i> <?php echo e($stats['tickets_trend']); ?>% from last week
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('asset-requests.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($stats['pending_requests'] ?? 0); ?></h3>
                            <p class="kpi-label">Pending Requests</p>
                            <?php if(isset($stats['requests_pending'])): ?>
                            <span class="kpi-trend neutral">
                                <i class="fa fa-clock-o"></i> Requires attention
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Security Status Alert -->
        <div class="row">
            <div class="col-md-12">
                <?php if(auth()->user()->email === 'daniel@quty.co.id'): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-shield"></i> Full Administrative Access</h4>
                    You have unrestricted access to all administrative functions including database modifications.
                    <?php if(session('admin_password_confirmed') && session('admin_password_confirmed') > now()->subMinutes(30)): ?>
                        <br><small><i class="fa fa-check-circle"></i> Password authentication valid until <?php echo e(session('admin_password_confirmed')->addMinutes(30)->format('H:i:s')); ?></small>
                        <a href="<?php echo e(route('admin.clear-auth')); ?>" class="btn btn-xs btn-default pull-right" onclick="return confirm('Clear authentication session?')">
                            <i class="fa fa-times"></i> Clear Auth
                        </a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Limited Administrative Access</h4>
                    Your account (<?php echo e(auth()->user()->email); ?>) has read-only admin access. 
                    Database modifications are restricted to authorized personnel (daniel@quty.co.id).
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <!-- System Status -->
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-cog"></i> System Status
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>Database:</strong></td>
                                <td>
                                    <span class="label label-success">
                                        <i class="fa fa-check"></i> Connected
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Cache:</strong></td>
                                <td>
                                    <span class="label label-<?php echo e($system_status['cache'] ? 'success' : 'warning'); ?>">
                                        <i class="fa fa-<?php echo e($system_status['cache'] ? 'check' : 'exclamation-triangle'); ?>"></i> 
                                        <?php echo e($system_status['cache'] ? 'Working' : 'Issues'); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Storage:</strong></td>
                                <td>
                                    <span class="label label-<?php echo e($system_status['storage'] ? 'success' : 'danger'); ?>">
                                        <i class="fa fa-<?php echo e($system_status['storage'] ? 'check' : 'times'); ?>"></i> 
                                        <?php echo e($system_status['storage'] ? 'Writable' : 'Read-only'); ?>

                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>PHP Version:</strong></td>
                                <td><span class="label label-info"><?php echo e(PHP_VERSION); ?></span></td>
                            </tr>
                            <tr>
                                <td><strong>Laravel Version:</strong></td>
                                <td><span class="label label-info"><?php echo e(app()->version()); ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-flash"></i> Quick Actions
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-app">
                                    <i class="fa fa-users"></i> Manage Users
                                </a>
                            </div>
                            <div class="col-xs-6">
                                <a href="<?php echo e(route('system.settings')); ?>" class="btn btn-app">
                                    <i class="fa fa-cogs"></i> System Settings
                                </a>
                            </div>
                            <div class="col-xs-6">
                                <a href="<?php echo e(route('admin.database.index')); ?>" class="btn btn-app">
                                    <i class="fa fa-database"></i> Database
                                    <?php if(auth()->user()->email !== 'daniel@quty.co.id'): ?>
                                        <span class="badge bg-orange"><i class="fa fa-eye"></i></span>
                                    <?php else: ?>
                                        <span class="badge bg-green"><i class="fa fa-edit"></i></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="col-xs-6">
                                <?php if(auth()->user()->email === 'daniel@quty.co.id'): ?>
                                <a href="<?php echo e(route('admin.cache')); ?>" class="btn btn-app">
                                    <i class="fa fa-refresh"></i> Clear Cache
                                    <span class="badge bg-green"><i class="fa fa-unlock"></i></span>
                                </a>
                                <?php else: ?>
                                <span class="btn btn-app disabled" title="Restricted to daniel@quty.co.id">
                                    <i class="fa fa-refresh"></i> Clear Cache
                                    <span class="badge bg-red"><i class="fa fa-lock"></i></span>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-history"></i> Recent System Activity
                        </h3>
                    </div>
                    <div class="box-body">
                        <?php if(isset($recent_activities) && count($recent_activities) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recent_activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($activity['time']); ?></td>
                                        <td><?php echo e($activity['user']); ?></td>
                                        <td>
                                            <span class="label label-<?php echo e($activity['type']); ?>">
                                                <?php echo e($activity['action']); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e($activity['details']); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No recent activity to display.</p>
                        <?php endif; ?>
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
    
    // Add click loading to quick action buttons
    $('.btn-app').on('click', function() {
        if (!$(this).hasClass('disabled')) {
            showLoadingOverlay('Loading...');
        }
    });
    
    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>