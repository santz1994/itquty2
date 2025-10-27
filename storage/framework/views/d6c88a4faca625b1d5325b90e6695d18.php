

<?php $__env->startSection('main-content'); ?>
<!-- Dashboard Overview -->
<div class="row">
    <!-- Quick Stats -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo e($stats['open_tickets'] ?? 0); ?></h3>
                <p>Open Tickets</p>
            </div>
            <div class="icon">
                <i class="fa fa-ticket"></i>
            </div>
            <a href="<?php echo e(url('/tickets')); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?php echo e($stats['overdue_tickets'] ?? 0); ?></h3>
                <p>Overdue Tickets</p>
            </div>
            <div class="icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <a href="<?php echo e(url('/tickets?filter=overdue')); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo e($stats['total_assets'] ?? 0); ?></h3>
                <p>Total Assets</p>
            </div>
            <div class="icon">
                <i class="fa fa-tags"></i>
            </div>
            <a href="<?php echo e(url('/assets')); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php echo e($stats['maintenance_due'] ?? 0); ?></h3>
                <p>Maintenance Due</p>
            </div>
            <div class="icon">
                <i class="fa fa-wrench"></i>
            </div>
            <a href="<?php echo e(url('/assets?filter=maintenance_due')); ?>" class="small-box-footer">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="row">
    <!-- Recent Tickets -->
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-ticket"></i> Recent Tickets
                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(url('/tickets/create')); ?>" class="btn btn-primary btn-xs">
                        <i class="fa fa-plus"></i> New Ticket
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php if(isset($recentTickets) && $recentTickets->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>SLA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('tickets.show', $ticket->id)); ?>">
                                            <?php echo e($ticket->ticket_code); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e(\Illuminate\Support\Str::limit($ticket->subject, 30)); ?></td>
                                    <td>
                                        <?php if($ticket->ticket_priority): ?>
                                            <span class="label label-<?php echo e($ticket->ticket_priority->color ?? 'default'); ?>">
                                                <?php echo e($ticket->ticket_priority->priority); ?>

                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($ticket->ticket_status): ?>
                                            <span class="label label-<?php echo e($ticket->ticket_status->color ?? 'default'); ?>">
                                                <?php echo e($ticket->ticket_status->status); ?>

                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($ticket->sla_due): ?>
                                            <?php
                                                $now = now();
                                                $slaClass = 'success';
                                                if ($ticket->sla_due->isPast()) $slaClass = 'danger';
                                                elseif ($ticket->sla_due->diffInHours($now) <= 2) $slaClass = 'warning';
                                            ?>
                                            <small class="label label-<?php echo e($slaClass); ?>">
                                                <?php echo e($ticket->sla_due->diffForHumans()); ?>

                                            </small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No recent tickets</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Asset Status -->
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-desktop"></i> Asset Status Overview
                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(url('/assets')); ?>" class="btn btn-success btn-xs">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php if(isset($assetStats)): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active</span>
                                    <span class="info-box-number"><?php echo e($assetStats['active'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-wrench"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">In Repair</span>
                                    <span class="info-box-number"><?php echo e($assetStats['in_repair'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-blue"><i class="fa fa-archive"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">In Stock</span>
                                    <span class="info-box-number"><?php echo e($assetStats['in_stock'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-red"><i class="fa fa-trash"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Disposed</span>
                                    <span class="info-box-number"><?php echo e($assetStats['disposed'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Daily Activities & Asset Maintenance -->
<div class="row">
    <!-- Today's Activities -->
    <div class="col-md-8">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-calendar-check-o"></i> Today's Activities
                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('daily-activities.create')); ?>" class="btn btn-info btn-xs">
                        <i class="fa fa-plus"></i> Add Activity
                    </a>
                    <a href="<?php echo e(route('daily-activities.index')); ?>" class="btn btn-default btn-xs">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php if(isset($todayActivities) && $todayActivities->count() > 0): ?>
                    <div class="timeline">
                        <?php $__currentLoopData = $todayActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="time-label">
                            <span class="bg-blue"><?php echo e($activity->created_at->format('H:i')); ?></span>
                        </div>
                        <div>
                            <i class="fa fa-<?php echo e($activity->type === 'manual' ? 'edit' : 'cogs'); ?> bg-<?php echo e($activity->type === 'manual' ? 'blue' : 'green'); ?>"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fa fa-clock-o"></i> <?php echo e($activity->created_at->diffForHumans()); ?>

                                </span>
                                <h3 class="timeline-header">
                                    <strong><?php echo e($activity->user->name); ?></strong>
                                    <?php if($activity->ticket): ?>
                                        - <a href="<?php echo e(route('tickets.show', $activity->ticket->id)); ?>"><?php echo e($activity->ticket->ticket_code); ?></a>
                                    <?php endif; ?>
                                </h3>
                                <div class="timeline-body">
                                    <?php echo e(\Illuminate\Support\Str::limit($activity->description, 150)); ?>

                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <i class="fa fa-clock-o bg-gray"></i>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <p class="text-muted">No activities logged today</p>
                        <a href="<?php echo e(route('daily-activities.create')); ?>" class="btn btn-info">
                            <i class="fa fa-plus"></i> Log Your First Activity
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alerts & Notifications -->
    <div class="col-md-4">
        <!-- SLA Alerts -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-exclamation-triangle"></i> SLA Alerts
                </h3>
            </div>
            <div class="box-body">
                <?php if(isset($slaAlerts) && $slaAlerts->count() > 0): ?>
                    <?php $__currentLoopData = $slaAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="alert alert-<?php echo e($alert->sla_status_color); ?> alert-dismissible">
                        <h4><?php echo e($alert->ticket_code); ?></h4>
                        <p><?php echo e(\Illuminate\Support\Str::limit($alert->subject, 50)); ?></p>
                        <small>Due: <?php echo e($alert->sla_due->format('d/m H:i')); ?></small>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p class="text-muted">No SLA alerts</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Asset Maintenance -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-wrench"></i> Maintenance Due
                </h3>
            </div>
            <div class="box-body">
                <?php if(isset($maintenanceDue) && $maintenanceDue->count() > 0): ?>
                    <?php $__currentLoopData = $maintenanceDue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="media">
                        <div class="media-left">
                            <i class="fa fa-desktop fa-2x text-yellow"></i>
                        </div>
                        <div class="media-body">
                            <h5 class="media-heading"><?php echo e($asset->asset_tag); ?></h5>
                            <p><?php echo e($asset->model->name ?? 'Unknown Model'); ?></p>
                            <small class="text-muted">Last maintenance: <?php echo e($asset->last_maintenance ?? 'Never'); ?></small>
                        </div>
                    </div>
                    <hr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p class="text-muted">No maintenance due</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-flash"></i> Quick Actions
                </h3>
            </div>
            <div class="box-body">
                <div class="btn-group-vertical btn-block">
                    <a href="<?php echo e(url('/tickets/create')); ?>" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Create Ticket
                    </a>
                    <a href="<?php echo e(url('/assets/create')); ?>" class="btn btn-success">
                        <i class="fa fa-plus"></i> Add Asset
                    </a>
                    <a href="<?php echo e(route('daily-activities.create')); ?>" class="btn btn-info">
                        <i class="fa fa-plus"></i> Log Activity
                    </a>
                    <a href="<?php echo e(url('/reports')); ?>" class="btn btn-warning">
                        <i class="fa fa-chart-bar"></i> View Reports
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Integration Status -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-cogs"></i> System Integration Status
                </h3>
                <div class="box-tools pull-right">
                    <a href="/comprehensive-test.html" class="btn btn-default btn-xs">
                        <i class="fa fa-check"></i> Run System Test
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-ticket"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Ticketing System</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-tags"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Asset Management</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Daily Activities</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-qrcode"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">QR Integration</span>
                                <span class="info-box-number">
                                    <i class="fa fa-check text-green"></i> Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
<script>
$(document).ready(function() {
    // Auto refresh dashboard every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Real-time clock
    function updateClock() {
        var now = new Date();
        var timeString = now.toLocaleTimeString();
        $('#current-time').text(timeString);
    }
    
    // Update clock every second if element exists
    if ($('#current-time').length) {
        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    }
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/dashboard/integrated-dashboard.blade.php ENDPATH**/ ?>