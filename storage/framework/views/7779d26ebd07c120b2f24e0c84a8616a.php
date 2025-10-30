

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-lemon-o"></i> Lemon Assets
                    </h3>
                    <div class="box-tools pull-right">
                        <a href="<?php echo e(route('asset-maintenance.index')); ?>" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <h4><i class="icon fa fa-warning"></i> Lemon Assets</h4>
                                These assets have had 3 or more maintenance tickets in the last month and may require replacement or intensive maintenance.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-yellow">
                                <span class="info-box-icon"><i class="fa fa-lemon-o"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Lemon Assets</span>
                                    <span class="info-box-number"><?php echo e($lemonStats['total'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-red">
                                <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">High Priority</span>
                                    <span class="info-box-number"><?php echo e($lemonStats['high_priority'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-orange">
                                <span class="info-box-icon"><i class="fa fa-wrench"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Avg Tickets/Asset</span>
                                    <span class="info-box-number"><?php echo e($lemonStats['avg_tickets'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-purple">
                                <span class="info-box-icon"><i class="fa fa-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Est. Replacement Cost</span>
                                    <span class="info-box-number">$<?php echo e(number_format($lemonStats['replacement_cost'] ?? 0)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-hover" id="lemon-assets-table">
                                <thead>
                                    <tr>
                                        <th>Asset Tag</th>
                                        <th>Model</th>
                                        <th>Serial Number</th>
                                        <th>Status</th>
                                        <th>Tickets (30 days)</th>
                                        <th>Last Ticket</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $lemonAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('assets.show', $asset->id)); ?>">
                                                <?php echo e($asset->asset_tag); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($asset->model->asset_model ?? 'Unknown'); ?></td>
                                        <td><?php echo e($asset->serial_number); ?></td>
                                        <td>
                                            <span class="label label-<?php echo e($asset->status->color ?? 'default'); ?>">
                                                <?php echo e($asset->status->name ?? 'Unknown'); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-red"><?php echo e($asset->recent_tickets_count); ?></span>
                                        </td>
                                        <td><?php echo e($asset->last_ticket_date ? $asset->last_ticket_date->format('Y-m-d') : 'N/A'); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('asset-maintenance.show', $asset->id)); ?>" class="btn btn-sm btn-info">
                                                <i class="fa fa-history"></i> History
                                            </a>
                                            <a href="<?php echo e(route('assets.edit', $asset->id)); ?>" class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fa fa-smile-o"></i> No lemon assets found! All assets are performing well.
                                        </td>
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

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#lemon-assets-table').DataTable({
        "order": [[ 4, "desc" ]],
        "pageLength": 25
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\asset-maintenance\lemon-assets.blade.php ENDPATH**/ ?>