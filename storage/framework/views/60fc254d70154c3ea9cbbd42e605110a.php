

<?php $__env->startSection('main-content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-chart-line"></i> SLA Dashboard</h2>
                <div>
                    <a href="<?php echo e(route('sla.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-cog"></i> Manage Policies
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('sla.dashboard')); ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="start_date" 
                                           name="start_date" 
                                           value="<?php echo e(request('start_date', now()->startOfMonth()->format('Y-m-d'))); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="end_date" 
                                           name="end_date" 
                                           value="<?php echo e(request('end_date', now()->format('Y-m-d'))); ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="priority_id">Priority</label>
                                    <select class="form-control" id="priority_id" name="priority_id">
                                        <option value="">All Priorities</option>
                                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($priority->id); ?>" 
                                                    <?php echo e(request('priority_id') == $priority->id ? 'selected' : ''); ?>>
                                                <?php echo e($priority->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="assigned_to">Assigned To</label>
                                    <select class="form-control" id="assigned_to" name="assigned_to">
                                        <option value="">All Users</option>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>" 
                                                    <?php echo e(request('assigned_to') == $user->id ? 'selected' : ''); ?>>
                                                <?php echo e($user->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Apply Filters
                                </button>
                                <a href="<?php echo e(route('sla.dashboard')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Total Tickets</h5>
                            <h2 class="mt-2 mb-0"><?php echo e($metrics['total_tickets']); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-ticket-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">SLA Met</h5>
                            <h2 class="mt-2 mb-0"><?php echo e($metrics['sla_met']); ?></h2>
                            <small><?php echo e($metrics['sla_compliance_rate']); ?>% compliance</small>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">SLA Breached</h5>
                            <h2 class="mt-2 mb-0"><?php echo e($metrics['sla_breached']); ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Critical Tickets</h5>
                            <h2 class="mt-2 mb-0"><?php echo e($metrics['critical_tickets']); ?></h2>
                            <small>Needs attention</small>
                        </div>
                        <div>
                            <i class="fas fa-fire fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Times -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-reply"></i> Average First Response Time
                    </h5>
                </div>
                <div class="card-body">
                    <h2 class="text-primary">
                        <?php echo e(number_format($metrics['avg_response_time_hours'], 1)); ?> hrs
                    </h2>
                    <p class="text-muted mb-0">
                        Time from ticket creation to first response
                        <br><small>(<?php echo e(number_format($metrics['avg_response_time_minutes'], 0)); ?> minutes)</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle"></i> Average Resolution Time
                    </h5>
                </div>
                <div class="card-body">
                    <h2 class="text-success">
                        <?php echo e(number_format($metrics['avg_resolution_time_hours'], 1)); ?> hrs
                    </h2>
                    <p class="text-muted mb-0">
                        Time from ticket creation to resolution
                        <br><small>(<?php echo e(number_format($metrics['avg_resolution_time_minutes'], 0)); ?> minutes)</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Breached Tickets -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle text-danger"></i> Breached SLA Tickets
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($breachedTickets->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Assigned To</th>
                                        <th>Created</th>
                                        <th>SLA Due</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $breachedTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><strong>#<?php echo e($ticket->id); ?></strong></td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($ticket->subject, 50)); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo e($ticket->priority->color ?? 'secondary'); ?>">
                                                    <?php echo e($ticket->priority->name ?? 'N/A'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php echo e($ticket->assignedTo->name ?? 'Unassigned'); ?>

                                            </td>
                                            <td><?php echo e($ticket->created_at->format('Y-m-d H:i')); ?></td>
                                            <td>
                                                <?php if($ticket->sla_due): ?>
                                                    <span class="text-danger">
                                                        <i class="fas fa-clock"></i>
                                                        <?php echo e($ticket->sla_due->format('Y-m-d H:i')); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">No SLA</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times-circle"></i> Breached
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('tickets.show', $ticket->id)); ?>" 
                                                   class="btn btn-sm btn-primary"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if($breachedTickets->hasPages()): ?>
                            <div class="mt-3">
                                <?php echo e($breachedTickets->appends(request()->query())->links()); ?>

                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <p>No breached SLA tickets found! Great work!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Tickets (At Risk) -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-fire text-warning"></i> Critical Tickets (At Risk of SLA Breach)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($criticalTickets->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Subject</th>
                                        <th>Priority</th>
                                        <th>Assigned To</th>
                                        <th>Created</th>
                                        <th>SLA Due</th>
                                        <th>Time Remaining</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $criticalTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $slaStatus = app(\App\Services\SlaTrackingService::class)->getSlaStatus($ticket);
                                        ?>
                                        <tr>
                                            <td><strong>#<?php echo e($ticket->id); ?></strong></td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($ticket->subject, 50)); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo e($ticket->priority->color ?? 'secondary'); ?>">
                                                    <?php echo e($ticket->priority->name ?? 'N/A'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <?php echo e($ticket->assignedTo->name ?? 'Unassigned'); ?>

                                            </td>
                                            <td><?php echo e($ticket->created_at->format('Y-m-d H:i')); ?></td>
                                            <td>
                                                <?php if($ticket->sla_due): ?>
                                                    <span class="text-warning">
                                                        <i class="fas fa-clock"></i>
                                                        <?php echo e($ticket->sla_due->format('Y-m-d H:i')); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">No SLA</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($slaStatus && isset($slaStatus['percentage_remaining'])): ?>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-<?php echo e($slaStatus['color']); ?>" 
                                                             role="progressbar" 
                                                             style="width: <?php echo e($slaStatus['percentage_remaining']); ?>%"
                                                             aria-valuenow="<?php echo e($slaStatus['percentage_remaining']); ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?php echo e(round($slaStatus['percentage_remaining'], 1)); ?>%
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo e($slaStatus['color'] ?? 'secondary'); ?>">
                                                    <i class="fas fa-<?php echo e($slaStatus['icon'] ?? 'question'); ?>"></i> 
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $slaStatus['status'] ?? 'unknown'))); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('tickets.show', $ticket->id)); ?>" 
                                                   class="btn btn-sm btn-primary"
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if($criticalTickets->hasPages()): ?>
                            <div class="mt-3">
                                <?php echo e($criticalTickets->appends(request()->query())->links()); ?>

                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-thumbs-up fa-3x mb-3 text-success"></i>
                            <p>No critical tickets at risk! Keep up the good work!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SLA Policy Summary -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-list"></i> Active SLA Policies
                    </h5>
                </div>
                <div class="card-body">
                    <?php if($activePolicies->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Priority</th>
                                        <th>Response Time</th>
                                        <th>Resolution Time</th>
                                        <th>Business Hours</th>
                                        <th>Escalation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $activePolicies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-<?php echo e($policy->priority->color ?? 'secondary'); ?>">
                                                    <?php echo e($policy->priority->name ?? 'N/A'); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($policy->response_time); ?> min</td>
                                            <td><?php echo e($policy->resolution_time); ?> min</td>
                                            <td>
                                                <?php if($policy->business_hours_only): ?>
                                                    <i class="fas fa-check text-success"></i> Yes
                                                <?php else: ?>
                                                    <i class="fas fa-times text-danger"></i> No (24/7)
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($policy->escalation_time): ?>
                                                    <?php echo e($policy->escalation_time); ?> min
                                                    <?php if($policy->escalateToUser): ?>
                                                        → <?php echo e($policy->escalateToUser->name); ?>

                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Not configured</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                            <p>No active SLA policies found. Please create policies to track SLAs.</p>
                            <a href="<?php echo e(route('sla.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create SLA Policy
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.opacity-50 {
    opacity: 0.5;
}
.card-body h2 {
    font-size: 2.5rem;
    font-weight: bold;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/sla/dashboard.blade.php ENDPATH**/ ?>