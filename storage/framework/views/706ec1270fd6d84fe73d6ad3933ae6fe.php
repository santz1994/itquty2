

<?php $__env->startSection('title', 'KPI Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/dashboard-widgets.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="content-wrapper">
    <?php echo $__env->make('components.page-header', [
        'title' => 'KPI Dashboard',
        'subtitle' => 'Key Performance Indicators & Metrics',
        'icon' => 'fa-line-chart',
        'breadcrumbs' => [
            ['label' => 'Home', 'url' => url('/home'), 'icon' => 'fa-dashboard'],
            ['label' => 'KPI Dashboard', 'active' => true]
        ]
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Performance Overview</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        
                        <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3><?php echo e($data['totalTickets']); ?></h3>
                                <p>Total Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-ticket"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3><?php echo e($data['openTickets']); ?></h3>
                                <p>Open Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-folder-open"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?php echo e($data['closedTickets']); ?></h3>
                                <p>Closed Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3><?php echo e($data['overdueTickets']); ?></h3>
                                <p>Overdue Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-clock-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Avg Resolution Time</span>
                                <span class="info-box-number"><?php echo e($data['avgResolutionTime']); ?> hours</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">SLA Compliance</span>
                                <span class="info-box-number"><?php echo e($data['slaCompliance']); ?>%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-purple"><i class="fa fa-desktop"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Assets</span>
                                <span class="info-box-number"><?php echo e($data['totalAssets']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-orange"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Team Members</span>
                                <span class="info-box-number"><?php echo e($data['teamPerformance']->count()); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tickets by Priority</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="priorityChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tickets by Status</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="statusChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 2 -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Monthly Ticket Trend (Last 12 Months)</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="trendChart" width="400" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Assets by Status</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="assetsChart" width="300" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Broken Assets -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Most Problematic Assets</h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Asset Tag</th>
                                            <th>Ticket Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $data['topBrokenAssets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($asset->asset_tag); ?></td>
                                            <td><span class="badge bg-red"><?php echo e($asset->ticket_count); ?></span></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="2" class="text-center">No data available</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Team Performance</h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Team Member</th>
                                            <th>Resolved Tickets</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $data['teamPerformance']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($member->name); ?></td>
                                            <td><span class="badge bg-green"><?php echo e($member->resolved_tickets); ?></span></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="2" class="text-center">No data available</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Recent Activities</h3>
                            </div>
                            <div class="box-body">
                                <ul class="timeline">
                                    <?php $__empty_1 = true; $__currentLoopData = $data['recentActivities']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> <?php echo e($activity->created_at->diffForHumans()); ?></span>
                                            <h3 class="timeline-header"><?php echo e($activity->user->name ?? 'System'); ?></h3>
                                            <div class="timeline-body">
                                                <?php echo e($activity->description); ?>

                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li>
                                        <div class="timeline-item">
                                            <div class="timeline-body">No recent activities</div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tickets by Priority Chart
const priorityCanvas = document.getElementById('priorityChart');
if (priorityCanvas) {
    const priorityCtx = priorityCanvas.getContext('2d');
    new Chart(priorityCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($data['ticketsByPriority']->keys()); ?>,
        datasets: [{
            data: <?php echo json_encode($data['ticketsByPriority']->values()); ?>,
            backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#28a745']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
    });
}

// Tickets by Status Chart
const statusCanvas = document.getElementById('statusChart');
if (statusCanvas) {
    const statusCtx = statusCanvas.getContext('2d');
    new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($data['ticketsByStatus']->keys()); ?>,
        datasets: [{
            data: <?php echo json_encode($data['ticketsByStatus']->values()); ?>,
            backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#6c757d']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
    });
}

// Monthly Trend Chart
const trendCanvas = document.getElementById('trendChart');
if (trendCanvas) {
    const trendCtx = trendCanvas.getContext('2d');
    new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($data['monthlyTicketTrend']->keys()); ?>,
        datasets: [{
            label: 'Tickets Created',
            data: <?php echo json_encode($data['monthlyTicketTrend']->values()); ?>,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
    });
}

// Assets by Status Chart
const assetsCanvas = document.getElementById('assetsChart');
if (assetsCanvas) {
    const assetsCtx = assetsCanvas.getContext('2d');
    new Chart(assetsCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($data['assetsBreakdown']->keys()); ?>,
        datasets: [{
            data: <?php echo json_encode($data['assetsBreakdown']->values()); ?>,
            backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8']
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
                </script>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/kpi/dashboard.blade.php ENDPATH**/ ?>