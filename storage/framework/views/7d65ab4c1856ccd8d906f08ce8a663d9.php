

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-briefcase"></i> My Assets
                </h3>
                <div class="box-tools pull-right">
                    <span class="label label-info"><?php echo e($assets->count()); ?> assets</span>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <?php if($assets->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="myAssetsTable">
                        <thead>
                            <tr>
                                <th>Asset Tag</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Model</th>
                                <th>Serial Number</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Assigned Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($asset->asset_tag); ?></strong>
                                    <?php if($asset->qr_code): ?>
                                    <br><small class="text-muted"><i class="fa fa-qrcode"></i> QR Available</small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($asset->name); ?></td>
                                <td>
                                    <?php if($asset->assetType): ?>
                                    <span class="label label-default"><?php echo e($asset->assetType->name); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($asset->model): ?>
                                    <?php echo e($asset->model->name); ?>

                                    <?php if($asset->model->manufacturer): ?>
                                    <br><small class="text-muted"><?php echo e($asset->model->manufacturer->name); ?></small>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($asset->serial ?? '-'); ?></td>
                                <td>
                                    <?php if($asset->location): ?>
                                    <i class="fa fa-map-marker"></i> <?php echo e($asset->location->name); ?>

                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($asset->status): ?>
                                    <span class="label" style="background-color: <?php echo e($asset->status->color ?? '#999'); ?>">
                                        <?php echo e($asset->status->name); ?>

                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($asset->assigned_at): ?>
                                    <?php echo e($asset->assigned_at->format('d M Y')); ?>

                                    <?php else: ?>
                                    <small class="text-muted">N/A</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(url('/assets/' . $asset->id)); ?>" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <?php if($asset->qr_code): ?>
                                    <a href="<?php echo e(url('/assets/' . $asset->id . '/qr-code')); ?>" class="btn btn-sm btn-default" title="View QR Code" target="_blank">
                                        <i class="fa fa-qrcode"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> You don't have any assets assigned to you at the moment.
                </div>
                <?php endif; ?>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    $('#myAssetsTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[7, "desc"]] // Sort by assigned date descending
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\assets\my-assets.blade.php ENDPATH**/ ?>