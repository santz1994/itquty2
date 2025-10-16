

<?php $__env->startSection('title', 'Admin Performance'); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-users"></i> Admin Performance Dashboard
                </h3>
            </div>
            <div class="box-body">
                <!-- Performance Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3><?php echo e($totalAdmins ?? 0); ?></h3>
                                <p>Total Admins</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?php echo e($activeAdmins ?? 0); ?></h3>
                                <p>Active Today</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3><?php echo e($resolvedTickets ?? 0); ?></h3>
                                <p>Tickets Resolved Today</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3><?php echo e(number_format($avgResponseTime ?? 0, 1)); ?>h</h3>
                                <p>Avg Response Time</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Performance Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Admin Performance Metrics</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="adminPerformanceTable">
                                        <thead>
                                            <tr>
                                                <th>Admin</th>
                                                <th>Role</th>
                                                <th>Tickets Assigned</th>
                                                <th>Tickets Resolved</th>
                                                <th>Resolution Rate</th>
                                                <th>Avg Response Time</th>
                                                <th>Last Activity</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($adminPerformance) && count($adminPerformance) > 0): ?>
                                                <?php $__currentLoopData = $adminPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                                    <td><?php echo e($admin->assigned_tickets ?? 0); ?></td>
                                                    <td><?php echo e($admin->resolved_tickets ?? 0); ?></td>
                                                    <td>
                                                        <?php
                                                            $rate = ($admin->assigned_tickets > 0) ? 
                                                                round(($admin->resolved_tickets / $admin->assigned_tickets) * 100, 1) : 0;
                                                        ?>
                                                        <span class="label <?php if($rate >= 80): ?> label-success <?php elseif($rate >= 60): ?> label-warning <?php else: ?> label-danger <?php endif; ?>">
                                                            <?php echo e($rate); ?>%
                                                        </span>
                                                    </td>
                                                    <td><?php echo e(number_format($admin->avg_response_time ?? 0, 1)); ?>h</td>
                                                    <td><?php echo e($admin->last_activity ? $admin->last_activity->diffForHumans() : 'Never'); ?></td>
                                                    <td>
                                                        <?php if(isset($admin->is_online) && $admin->is_online): ?>
                                                            <span class="label label-success">Online</span>
                                                        <?php else: ?>
                                                            <span class="label label-default">Offline</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">No admin performance data available</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Charts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Weekly Ticket Resolution</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="weeklyResolutionChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Response Time Trends</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="responseTimeChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Recent Admin Activities</h3>
                            </div>
                            <div class="box-body">
                                <ul class="timeline">
                                    <?php if(isset($recentActivities) && count($recentActivities) > 0): ?>
                                        <?php $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li>
                                            <i class="fa fa-user bg-blue"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fa fa-clock-o"></i> <?php echo e($activity->created_at->diffForHumans()); ?></span>
                                                <h3 class="timeline-header"><?php echo e($activity->user->name ?? 'Unknown'); ?></h3>
                                                <div class="timeline-body">
                                                    <?php echo e($activity->description ?? 'No description available'); ?>

                                                </div>
                                            </div>
                                        </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <li>
                                            <i class="fa fa-info bg-gray"></i>
                                            <div class="timeline-item">
                                                <div class="timeline-body">
                                                    No recent activities found.
                                                </div>
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
<script src="<?php echo e(asset('plugins/chartjs/Chart.min.js')); ?>"></script>
<script src="<?php echo e(asset('plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('plugins/datatables/dataTables.bootstrap.min.js')); ?>"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#adminPerformanceTable').DataTable({
        responsive: true,
        order: [[4, 'desc']], // Sort by resolution rate
        pageLength: 10
    });

    // Weekly Resolution Chart
    var weeklyCtx = document.getElementById('weeklyResolutionChart');
    if (weeklyCtx) {
        var weeklyChart = new Chart(weeklyCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($weeklyLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']); ?>,
                datasets: [{
                    label: 'Tickets Resolved',
                    data: <?php echo json_encode($weeklyData ?? [5, 8, 12, 6, 15, 3, 2]); ?>,
                    borderColor: '#00a65a',
                    backgroundColor: 'rgba(0, 166, 90, 0.1)',
                    fill: true
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

    // Response Time Chart
    var responseCtx = document.getElementById('responseTimeChart');
    if (responseCtx) {
        var responseChart = new Chart(responseCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($responseLabels ?? ['< 1h', '1-4h', '4-8h', '8-24h', '> 24h']); ?>,
                datasets: [{
                    label: 'Number of Tickets',
                    data: <?php echo json_encode($responseData ?? [15, 25, 10, 8, 3]); ?>,
                    backgroundColor: [
                        '#00a65a',
                        '#f39c12',
                        '#3c8dbc',
                        '#dd4b39',
                        '#932ab6'
                    ]
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
});
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('plugins/datatables/dataTables.bootstrap.css')); ?>">
<style>
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #ddd;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.timeline > li {
    position: relative;
    margin-right: 10px;
    margin-bottom: 15px;
}

.timeline > li:before,
.timeline > li:after {
    content: "";
    display: table;
}

.timeline > li:after {
    clear: both;
}

.timeline > li > .timeline-item {
    -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    border-radius: 3px;
    margin-top: 0;
    background: #fff;
    color: #444;
    margin-left: 60px;
    margin-right: 15px;
    padding: 0;
    position: relative;
}

.timeline > li > .fa,
.timeline > li > .glyphicon,
.timeline > li > .ion {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/management/admin-performance.blade.php ENDPATH**/ ?>