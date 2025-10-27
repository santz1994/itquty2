

<?php $__env->startSection('main-content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Asset Request <?php echo e($assetRequest->id ?? $asset_request->id ?? '—'); ?></h3>
                    <div class="card-tools">
                        <?php if(Route::has('asset-requests.index')): ?>
                            <a href="<?php echo e(route('asset-requests.index')); ?>" class="btn btn-sm btn-secondary">Back to requests</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Requested for asset</dt>
                        <dd class="col-sm-9">
                            <?php if(optional($assetRequest)->asset): ?>
                                <a href="<?php echo e(Route::has('assets.show') ? route('assets.show', $assetRequest->asset) : '#'); ?>"><?php echo e($assetRequest->asset->asset_tag ?? $assetRequest->asset->id ?? '—'); ?></a>
                            <?php else: ?>
                                <?php echo e($assetRequest->asset_tag ?? '—'); ?>

                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-3">Requested by</dt>
                        <dd class="col-sm-9"><?php echo e(optional($assetRequest->requester)->name ?? optional($assetRequest->user)->name ?? '—'); ?></dd>

                        <dt class="col-sm-3">Quantity</dt>
                        <dd class="col-sm-9"><?php echo e($assetRequest->quantity ?? '1'); ?></dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9"><?php echo e($assetRequest->status->name ?? $assetRequest->status ?? '—'); ?></dd>

                        <dt class="col-sm-3">Created</dt>
                        <dd class="col-sm-9"><?php echo e(optional($assetRequest->created_at)->format('Y-m-d H:i') ?? '—'); ?></dd>

                        <dt class="col-sm-3">Updated</dt>
                        <dd class="col-sm-9"><?php echo e(optional($assetRequest->updated_at)->format('Y-m-d H:i') ?? '—'); ?></dd>

                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9"><?php echo e($assetRequest->notes ?? '—'); ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/asset-requests/show.blade.php ENDPATH**/ ?>