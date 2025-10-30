

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/dashboard-widgets.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
    <?php echo $__env->make('components.page-header', [
        'title' => 'Management Dashboard',
        'subtitle' => 'Strategic overview and KPI metrics',
        'icon' => 'fa-briefcase',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => url('/home'), 'icon' => 'fa-dashboard'],
            ['label' => 'Management Dashboard', 'active' => true]
        ]
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="content">
        <!-- Modern KPI Cards - Row 1: Tickets Overview -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('tickets.index', ['filter' => 'today'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-primary">
                            <i class="fa fa-ticket"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($overview['total_tickets_today'] ?? 0); ?></h3>
                            <p class="kpi-label">Today's Tickets</p>
                            <span class="kpi-trend neutral">
                                <i class="fa fa-calendar-o"></i> Real-time count
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('tickets.index', ['filter' => 'month'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-info">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($overview['total_tickets_month'] ?? 0); ?></h3>
                            <p class="kpi-label">This Month</p>
                            <?php if(isset($overview['month_growth']) && $overview['month_growth'] > 0): ?>
                            <span class="kpi-trend positive">
                                <i class="fa fa-arrow-up"></i> <?php echo e($overview['month_growth']); ?>% from last month
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('tickets.index', ['status' => 'overdue'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($overview['overdue_tickets'] ?? 0); ?></h3>
                            <p class="kpi-label">Overdue Tickets</p>
                            <span class="kpi-trend negative">
                                <i class="fa fa-warning"></i> Requires attention
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('tickets.index', ['status' => 'unassigned'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-warning">
                            <i class="fa fa-question-circle"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($overview['unassigned_tickets'] ?? 0); ?></h3>
                            <p class="kpi-label">Unassigned</p>
                            <span class="kpi-trend neutral">
                                <i class="fa fa-user-plus"></i> Needs assignment
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Overdue Tickets Widget Row -->
        <div class="row">
            <?php echo $__env->make('management.widgets.overdue-tickets', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>

        <!-- Modern KPI Cards - Row 2: System Overview -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('assets.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-success">
                            <i class="fa fa-desktop"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($overview['total_assets'] ?? 0); ?></h3>
                            <p class="kpi-label">Total Assets</p>
                            <?php if(isset($overview['assets_growth']) && $overview['assets_growth'] > 0): ?>
                            <span class="kpi-trend positive">
                                <i class="fa fa-arrow-up"></i> <?php echo e($overview['assets_growth']); ?>% growth
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('users.index')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-purple">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($overview['active_admins'] ?? 0); ?></h3>
                            <p class="kpi-label">Active Admins</p>
                            <span class="kpi-trend positive">
                                <i class="fa fa-circle"></i> Online now
                            </span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('sla.dashboard')); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon <?php echo e(($sla_compliance['compliance_rate'] ?? 0) >= 90 ? 'bg-success' : 'bg-warning'); ?>">
                            <i class="fa fa-pie-chart"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($sla_compliance['compliance_rate'] ?? 0); ?>%</h3>
                            <p class="kpi-label">SLA Compliance</p>
                            <?php if(($sla_compliance['compliance_rate'] ?? 0) >= 90): ?>
                            <span class="kpi-trend positive">
                                <i class="fa fa-check-circle"></i> Excellent performance
                            </span>
                            <?php else: ?>
                            <span class="kpi-trend negative">
                                <i class="fa fa-warning"></i> Needs improvement
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <a href="<?php echo e(route('assets.index', ['status' => 'in_use'])); ?>" style="text-decoration: none; color: inherit;">
                    <div class="kpi-card">
                        <div class="kpi-icon bg-teal">
                            <i class="fa fa-cogs"></i>
                        </div>
                        <div class="kpi-content">
                            <h3 class="kpi-value"><?php echo e($asset_overview['in_use'] ?? 0); ?></h3>
                            <p class="kpi-label">Assets In Use</p>
                            <?php if(isset($asset_overview['utilization_rate'])): ?>
                            <span class="kpi-trend neutral">
                                <i class="fa fa-percent"></i> <?php echo e($asset_overview['utilization_rate']); ?>% utilization
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Charts and Performance -->
        <div class="row">
            <!-- Ticket Trends Chart -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Ticket Trends (Last 30 Days)</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <canvas id="ticketTrendsChart" width="400" height="150"></canvas>
                    </div>
                </div>
            </div>

            <!-- Asset Overview -->
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Asset Status Distribution</h3>
                    </div>
                    <div class="box-body">
                        <canvas id="assetStatusChart" width="200" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Performance Summary -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Top Performing Admins</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo e(route('management.admin-performance')); ?>" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Admin</th>
                                        <th>Role</th>
                                        <th>Tickets Assigned</th>
                                        <th>Tickets Resolved</th>
                                        <th>Resolution Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(isset($admin_performance) && count($admin_performance) > 0): ?>
                                        <?php $__currentLoopData = array_slice($admin_performance, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $performanceData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $admin = $performanceData['admin'];
                                                $metrics = $performanceData['metrics'] ?? [];
                                            ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo e(asset('img/avatar.png')); ?>" class="img-circle" style="width: 30px; height: 30px;">
                                                    <?php echo e($admin->name); ?>

                                                </td>
                                                <td>
                                                    <?php if($admin->roles->count() > 0): ?>
                                                        <?php $__currentLoopData = $admin->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <span class="label label-primary"><?php echo e($role->name); ?></span>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php else: ?>
                                                        <span class="label label-default">No Role</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($metrics['total_assigned'] ?? 0); ?></td>
                                                <td><?php echo e($metrics['total_completed'] ?? 0); ?></td>
                                                <td>
                                                    <?php
                                                        $rate = $metrics['completion_rate'] ?? 0;
                                                    ?>
                                                    <span class="label <?php if($rate >= 80): ?> label-success <?php elseif($rate >= 60): ?> label-warning <?php else: ?> label-danger <?php endif; ?>">
                                                        <?php echo e(number_format($rate, 1)); ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($admin->adminOnlineStatus && $admin->adminOnlineStatus->is_online): ?>
                                                        <span class="label label-success">Online</span>
                                                    <?php else: ?>
                                                        <span class="label label-default">Offline</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No admin performance data available</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quick Actions</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo e(route('management.admin-performance')); ?>" class="btn btn-app">
                                    <i class="fa fa-users"></i> Admin Performance
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo e(route('management.ticket-reports')); ?>" class="btn btn-app">
                                    <i class="fa fa-ticket"></i> Ticket Reports
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo e(route('management.asset-reports')); ?>" class="btn btn-app">
                                    <i class="fa fa-desktop"></i> Asset Reports
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo e(url('/system/logs')); ?>" class="btn btn-app">
                                    <i class="fa fa-file-text"></i> System Logs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('plugins/chartjs/Chart.min.js')); ?>"></script>

<script>
$(document).ready(function() {
    // Ticket Trends Chart
    var trendsCtx = document.getElementById('ticketTrendsChart');
    if (trendsCtx) {
        var ticketTrendsChart = new Chart(trendsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($ticket_trends->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('M d'); })); ?>,
                datasets: [{
                    label: 'Created',
                    data: <?php echo json_encode($ticket_trends->pluck('created')); ?>,
                    borderColor: 'rgb(60, 141, 188)',
                    backgroundColor: 'rgba(60, 141, 188, 0.1)',
                    tension: 0.1
                }, {
                    label: 'Resolved',
                    data: <?php echo json_encode($ticket_trends->pluck('resolved')); ?>,
                    borderColor: 'rgb(0, 166, 90)',
                    backgroundColor: 'rgba(0, 166, 90, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Asset Status Chart
    var assetCtx = document.getElementById('assetStatusChart');
    if (assetCtx) {
        var assetChart = new Chart(assetCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['In Use', 'In Stock', 'In Repair', 'Disposed'],
                datasets: [{
                    data: [
                        <?php echo e($asset_overview['in_use'] ?? 0); ?>,
                        <?php echo e($asset_overview['in_stock'] ?? 0); ?>,
                        <?php echo e($asset_overview['in_repair'] ?? 0); ?>,
                        <?php echo e($asset_overview['disposed'] ?? 0); ?>

                    ],
                    backgroundColor: [
                        '#00a65a',
                        '#3c8dbc',
                        '#f39c12',
                        '#dd4b39'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    // Hide loading overlay when charts are loaded
    window.addEventListener('load', function() {
        setTimeout(function() {
            hideLoadingOverlay();
        }, 500);
    });
    
    // Tooltip initialization
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/management/dashboard.blade.php ENDPATH**/ ?>