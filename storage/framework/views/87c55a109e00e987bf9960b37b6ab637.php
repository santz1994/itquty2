

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-wrench"></i> Asset Maintenance Management
                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('asset-maintenance.analytics')); ?>" class="btn btn-info btn-sm">
                        <i class="fa fa-chart-bar"></i> Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Overview -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php echo e($stats['assets_needing_maintenance']); ?></h3>
                <p>Assets Need Maintenance</p>
            </div>
            <div class="icon">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3><?php echo e($stats['scheduled_maintenance']); ?></h3>
                <p>Scheduled Maintenance</p>
            </div>
            <div class="icon">
                <i class="fa fa-calendar"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?php echo e($stats['high_priority']); ?></h3>
                <p>High Priority</p>
            </div>
            <div class="icon">
                <i class="fa fa-fire"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3><?php echo e($stats['overdue_maintenance']); ?></h3>
                <p>Overdue Maintenance</p>
            </div>
            <div class="icon">
                <i class="fa fa-clock-o"></i>
            </div>
        </div>
    </div>
</div>

<!-- Assets Requiring Maintenance -->
<div class="row">
    <div class="col-md-8">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-exclamation-triangle"></i> Assets Requiring Maintenance
                </h3>
            </div>
            <div class="box-body">
                <?php if($assetsRequiringMaintenance->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Asset Tag</th>
                                    <th>Model</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Recent Issues</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $assetsRequiringMaintenance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($asset->asset_tag); ?></strong>
                                        <?php if($asset->is_lemon_asset): ?>
                                            <span class="label label-danger">Problematic</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($asset->model->name ?? 'Unknown'); ?></td>
                                    <td><?php echo e($asset->location->location_name ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="label label-<?php echo e($asset->status->color ?? 'default'); ?>">
                                            <?php echo e($asset->status->status ?? 'Unknown'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                            $recentTickets = $asset->tickets()->where('created_at', '>=', now()->subMonths(1))->count();
                                        ?>
                                        <span class="badge bg-<?php echo e($recentTickets >= 3 ? 'red' : ($recentTickets >= 2 ? 'yellow' : 'green')); ?>">
                                            <?php echo e($recentTickets); ?> tickets
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-xs">
                                            <a href="<?php echo e(route('asset-maintenance.show', $asset->id)); ?>" 
                                               class="btn btn-info" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-warning" 
                                                    onclick="createMaintenanceTicket(<?php echo e($asset->id); ?>)" 
                                                    title="Create Maintenance Ticket">
                                                <i class="fa fa-wrench"></i>
                                            </button>
                                            <?php if($asset->is_lemon_asset): ?>
                                                <button type="button" class="btn btn-danger" 
                                                        onclick="generateReplacementRequest(<?php echo e($asset->id); ?>)" 
                                                        title="Request Replacement">
                                                    <i class="fa fa-exchange"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success text-center">
                        <h4><i class="fa fa-check-circle"></i> All Assets in Good Condition</h4>
                        <p>No assets currently require maintenance attention.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Maintenance Schedule -->
    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-calendar"></i> Maintenance Schedule
                </h3>
            </div>
            <div class="box-body">
                <?php if($maintenanceSchedule->count() > 0): ?>
                    <?php $__currentLoopData = $maintenanceSchedule->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="media maintenance-item">
                        <div class="media-left">
                            <i class="fa fa-<?php echo e($schedule['priority'] === 'high' ? 'fire text-red' : 'wrench text-yellow'); ?> fa-2x"></i>
                        </div>
                        <div class="media-body">
                            <h5 class="media-heading">
                                <?php echo e($schedule['asset']->asset_tag); ?>

                                <span class="label label-<?php echo e($schedule['priority'] === 'high' ? 'danger' : 'warning'); ?>">
                                    <?php echo e(ucfirst($schedule['priority'])); ?>

                                </span>
                            </h5>
                            <p class="text-muted"><?php echo e($schedule['asset']->model->name ?? 'Unknown Model'); ?></p>
                            <small class="text-info">
                                <i class="fa fa-calendar"></i> Due: <?php echo e($schedule['recommended_date']->format('d/m/Y')); ?>

                            </small>
                            <br>
                            <small class="text-muted"><?php echo e($schedule['reason']); ?></small>
                        </div>
                    </div>
                    <hr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($maintenanceSchedule->count() > 10): ?>
                        <div class="text-center">
                            <small class="text-muted">And <?php echo e($maintenanceSchedule->count() - 10); ?> more items...</small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted text-center">No scheduled maintenance at this time.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Create Maintenance Ticket Modal -->
<div class="modal fade" id="maintenanceTicketModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create Maintenance Ticket</h4>
            </div>
            <form id="maintenanceTicketForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Asset</label>
                        <input type="text" id="assetDisplay" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority_id" class="form-control" required>
                            <option value="">Select Priority</option>
                            <?php $__currentLoopData = $priorities ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($priority->id); ?>"><?php echo e($priority->priority); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type_id" class="form-control" required>
                            <option value="">Select Type</option>
                            <?php $__currentLoopData = $ticketTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type->id); ?>"><?php echo e($type->type); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" required
                                  placeholder="Describe the maintenance issue or requirement..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Replacement Request Modal -->
<div class="modal fade" id="replacementRequestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">  
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Generate Replacement Request</h4>
            </div>
            <form id="replacementRequestForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Asset</label>
                        <input type="text" id="replacementAssetDisplay" class="form-control" readonly>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h5><i class="fa fa-exclamation-triangle"></i> Replacement Justification</h5>
                        <p>This asset has been identified as problematic based on maintenance history. 
                           Please provide justification for replacement request.</p>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason for Replacement</label>
                        <textarea name="reason" class="form-control" rows="4" required
                                  placeholder="Provide detailed justification for asset replacement..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Generate Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
<script>
function createMaintenanceTicket(assetId) {
    // Get asset details via AJAX (you would implement this endpoint)
    $.get('/api/assets/' + assetId, function(asset) {
        $('#assetDisplay').val(asset.asset_tag + ' - ' + asset.model.name);
        $('#maintenanceTicketForm').attr('action', '/asset-maintenance/' + assetId + '/create-ticket');
        $('input[name="subject"]').val('Maintenance required for ' + asset.asset_tag);
        $('#maintenanceTicketModal').modal('show');
    });
}

function generateReplacementRequest(assetId) {
    // Get asset details via AJAX
    $.get('/api/assets/' + assetId, function(asset) {
        $('#replacementAssetDisplay').val(asset.asset_tag + ' - ' + asset.model.name);
        $('#replacementRequestForm').attr('action', '/asset-maintenance/' + assetId + '/replacement-request');
        $('#replacementRequestModal').modal('show');
    });
}

$(document).ready(function() {
    // Auto-refresh every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/asset-maintenance/index.blade.php ENDPATH**/ ?>