

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Maintenance Log Details</h3>
                    <div class="box-tools pull-right">
                        <a href="<?php echo e(route('maintenance.index')); ?>" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Maintenance Logs
                        </a>
                        <a href="<?php echo e(route('maintenance.edit', $maintenanceLog->id)); ?>" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <!-- Left Column: Asset Information -->
                        <div class="col-md-6">
                            <h4><i class="fa fa-laptop"></i> Asset Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Asset Tag:</th>
                                    <td>
                                        <a href="<?php echo e(route('assets.show', $maintenanceLog->asset->id)); ?>">
                                            <?php echo e($maintenanceLog->asset->asset_tag); ?>

                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Asset Name:</th>
                                    <td><?php echo e($maintenanceLog->asset->name); ?></td>
                                </tr>
                                <tr>
                                    <th>Model:</th>
                                    <td><?php echo e($maintenanceLog->asset->model->asset_model ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Serial Number:</th>
                                    <td><?php echo e($maintenanceLog->asset->serial_number ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td><?php echo e($maintenanceLog->asset->location->name ?? 'N/A'); ?></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Right Column: Maintenance Information -->
                        <div class="col-md-6">
                            <h4><i class="fa fa-wrench"></i> Maintenance Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Maintenance Date:</th>
                                    <td><?php echo e(\Carbon\Carbon::parse($maintenanceLog->maintenance_date)->format('l, j F Y')); ?></td>
                                </tr>
                                <tr>
                                    <th>Maintenance Type:</th>
                                    <td>
                                        <span class="label label-<?php echo e($maintenanceLog->maintenance_type === 'preventive' ? 'info' : 'warning'); ?>">
                                            <?php echo e(ucfirst($maintenanceLog->maintenance_type)); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Performed By:</th>
                                    <td><?php echo e($maintenanceLog->performedBy->name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Cost:</th>
                                    <td>
                                        <?php if($maintenanceLog->cost): ?>
                                            Rp <?php echo e(number_format($maintenanceLog->cost, 2)); ?>

                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if($maintenanceLog->ticket_id): ?>
                                <tr>
                                    <th>Related Ticket:</th>
                                    <td>
                                        <a href="<?php echo e(route('tickets.show', $maintenanceLog->ticket_id)); ?>">
                                            <?php echo e($maintenanceLog->ticket->ticket_code ?? 'View Ticket'); ?>

                                        </a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Full Width: Description -->
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <h4><i class="fa fa-file-text"></i> Description</h4>
                            <div class="well">
                                <?php echo nl2br(e($maintenanceLog->description ?? 'No description provided.')); ?>

                            </div>
                        </div>
                    </div>

                    <!-- Full Width: Notes -->
                    <?php if($maintenanceLog->notes): ?>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <h4><i class="fa fa-sticky-note"></i> Notes</h4>
                            <div class="well">
                                <?php echo nl2br(e($maintenanceLog->notes)); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Timestamps -->
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <small class="text-muted">
                                <i class="fa fa-clock-o"></i> Created: <?php echo e($maintenanceLog->created_at->format('Y-m-d H:i:s')); ?>

                                <?php if($maintenanceLog->updated_at->ne($maintenanceLog->created_at)): ?>
                                    | Updated: <?php echo e($maintenanceLog->updated_at->format('Y-m-d H:i:s')); ?>

                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Attachments Section -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-paperclip"></i> Maintenance Attachments</h3>
                </div>
                <div class="box-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#before-photos" aria-controls="before-photos" role="tab" data-toggle="tab">
                                <i class="fa fa-camera"></i> Before Photos
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#after-photos" aria-controls="after-photos" role="tab" data-toggle="tab">
                                <i class="fa fa-camera"></i> After Photos
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#receipts" aria-controls="receipts" role="tab" data-toggle="tab">
                                <i class="fa fa-file-text"></i> Receipts
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" style="padding-top: 20px;">
                        <!-- Before Photos Tab -->
                        <div role="tabpanel" class="tab-pane active" id="before-photos">
                            <?php echo $__env->make('partials.file-uploader', [
                                'model_type' => 'maintenance_log',
                                'model_id' => $maintenanceLog->id,
                                'collection' => 'before_photos'
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>

                        <!-- After Photos Tab -->
                        <div role="tabpanel" class="tab-pane" id="after-photos">
                            <?php echo $__env->make('partials.file-uploader', [
                                'model_type' => 'maintenance_log',
                                'model_id' => $maintenanceLog->id,
                                'collection' => 'after_photos'
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>

                        <!-- Receipts Tab -->
                        <div role="tabpanel" class="tab-pane" id="receipts">
                            <?php echo $__env->make('partials.file-uploader', [
                                'model_type' => 'maintenance_log',
                                'model_id' => $maintenanceLog->id,
                                'collection' => 'receipts'
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\maintenance\show.blade.php ENDPATH**/ ?>