

<?php $__env->startSection('main-content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Movements for asset: <?php echo e($asset->asset_tag ?? '—'); ?></h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('assets.show', $asset)); ?>" class="btn btn-sm btn-secondary">Back to asset</a>
                    </div>
                </div>

                <div class="card-body">
                    <?php if($movements->isEmpty()): ?>
                        <p>No movements found for this asset.</p>
                    <?php else: ?>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Moved By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(optional($m->created_at)->format('Y-m-d H:i')); ?></td>
                                        <td><?php echo e(optional($m->from_location)->location_name ?? '—'); ?></td>
                                        <td><?php echo e(optional($m->to_location)->location_name ?? '—'); ?></td>
                                        <td><?php echo e(optional($m->moved_by)->name ?? (optional($m->user)->name ?? '—')); ?></td>
                                        <td><?php echo e($m->notes ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/assets/movements.blade.php ENDPATH**/ ?>