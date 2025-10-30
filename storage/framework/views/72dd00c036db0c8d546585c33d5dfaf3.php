

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Asset Maintenance History - <?php echo e($asset->name); ?></h3>
                    <div class="box-tools pull-right">
                        <a href="<?php echo e(route('asset-maintenance.index')); ?>" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Maintenance Dashboard
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Asset Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Asset Tag:</th>
                                    <td><?php echo e($asset->asset_tag); ?></td>
                                </tr>
                                <tr>
                                    <th>Model:</th>
                                    <td><?php echo e($asset->model->asset_model ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Serial Number:</th>
                                    <td><?php echo e($asset->serial_number); ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><?php echo e($asset->status->name ?? 'N/A'); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-8">
                            <h4>Maintenance History</h4>
                            <?php if($history->isEmpty()): ?>
                                <p class="text-muted">No maintenance records found for this asset.</p>
                            <?php else: ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Ticket</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($record->created_at->format('Y-m-d')); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('tickets.show', $record->id)); ?>">
                                                    <?php echo e($record->ticket_code); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($record->description, 50)); ?></td>
                                            <td>
                                                <span class="label label-<?php echo e($record->ticket_status->color ?? 'default'); ?>">
                                                    <?php echo e($record->ticket_status->name ?? 'Unknown'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <span class="label label-<?php echo e($record->ticket_priority->color ?? 'default'); ?>">
                                                    <?php echo e($record->ticket_priority->name ?? 'Unknown'); ?>

                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\asset-maintenance\show.blade.php ENDPATH**/ ?>