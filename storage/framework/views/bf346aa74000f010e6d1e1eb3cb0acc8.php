 

<?php $__env->startSection('title', 'Ticket Reports'); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-bar-chart"></i> Ticket Reports
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <!-- Total Tickets Card -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?php echo e($totalTickets ?? 0); ?></h3>
                                <p>Total Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-ticket"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Open Tickets Card -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3><?php echo e($openTickets ?? 0); ?></h3>
                                <p>Open Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-folder-open"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Closed Tickets Card -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3><?php echo e($closedTickets ?? 0); ?></h3>
                                <p>Closed Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <!-- High Priority Card -->
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3><?php echo e($highPriorityTickets ?? 0); ?></h3>
                                <p>High Priority</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tickets by Status Chart -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tickets by Status</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="ticketStatusChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets by Priority Chart -->
                    <div class="col-md-6">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tickets by Priority</h3>
                            </div>
                            <div class="box-body">
                                <canvas id="ticketPriorityChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tickets Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Recent Tickets</h3>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Title</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                                <th>Assigned To</th>
                                                <th>Created</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(isset($recentTickets) && count($recentTickets) > 0): ?>
                                                <?php $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($ticket->id); ?></td>
                                                    <td><?php echo e($ticket->title); ?></td>
                                                    <td>
                                                        <span class="label label-info"><?php echo e($ticket->status->name ?? 'N/A'); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning"><?php echo e($ticket->priority->name ?? 'N/A'); ?></span>
                                                    </td>
                                                    <td><?php echo e($ticket->assignedTo->name ?? 'Unassigned'); ?></td>
                                                    <td><?php echo e($ticket->created_at->format('Y-m-d H:i')); ?></td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No tickets found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
<script>
$(document).ready(function() {
    // Tickets by Status Chart
    var statusCtx = document.getElementById('ticketStatusChart');
    if (statusCtx) {
        var statusChart = new Chart(statusCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($ticketsByStatus['labels'] ?? ['Open', 'Closed', 'In Progress']); ?>,
                datasets: [{
                    data: <?php echo json_encode($ticketsByStatus['data'] ?? [10, 5, 3]); ?>,
                    backgroundColor: [
                        '#f39c12',
                        '#00a65a',
                        '#3c8dbc'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Tickets by Priority Chart
    var priorityCtx = document.getElementById('ticketPriorityChart');
    if (priorityCtx) {
        var priorityChart = new Chart(priorityCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($ticketsByPriority['labels'] ?? ['Low', 'Medium', 'High', 'Critical']); ?>,
                datasets: [{
                    label: 'Tickets',
                    data: <?php echo json_encode($ticketsByPriority['data'] ?? [5, 8, 3, 2]); ?>,
                    backgroundColor: [
                        '#00a65a',
                        '#f39c12',
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\management\ticket-reports.blade.php ENDPATH**/ ?>