

<?php $__env->startSection('main-content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Asset Request #<?php echo e($assetRequest->id); ?></h3>
                    <div class="card-tools">
                        <?php if($assetRequest->status === 'pending' && Auth::user() && $assetRequest->requested_by === Auth::id()): ?>
                            <a href="<?php echo e(route('asset-requests.edit', $assetRequest->id)); ?>" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        <?php endif; ?>
                        <?php if(Route::has('asset-requests.index')): ?>
                            <a href="<?php echo e(route('asset-requests.index')); ?>" class="btn btn-sm btn-secondary">Back to requests</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="mb-3">Request Details</h4>
                            <dl class="row">
                                <dt class="col-sm-3">Request ID</dt>
                                <dd class="col-sm-9">#<?php echo e($assetRequest->id); ?> <?php if($assetRequest->request_number): ?> <small class="text-muted">(<?php echo e($assetRequest->request_number); ?>)</small> <?php endif; ?></dd>

                                <dt class="col-sm-3">Asset Type</dt>
                                <dd class="col-sm-9">
                                    <?php if($assetRequest->assetType): ?>
                                        <span class="badge badge-info"><?php echo e($assetRequest->assetType->type_name ?? $assetRequest->assetType->name ?? 'N/A'); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-3">Requested By</dt>
                                <dd class="col-sm-9">
                                    <?php if($assetRequest->requestedBy): ?>
                                        <strong><?php echo e($assetRequest->requestedBy->name); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e($assetRequest->requestedBy->email); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Unknown user</span>
                                    <?php endif; ?>
                                </dd>

                                <dt class="col-sm-3">Status</dt>
                                <dd class="col-sm-9">
                                    <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'fulfilled' => 'primary'
                                        ];
                                        $statusColor = $statusColors[$assetRequest->status] ?? 'secondary';
                                    ?>
                                    <span class="badge badge-<?php echo e($statusColor); ?>"><?php echo e(ucfirst($assetRequest->status)); ?></span>
                                </dd>

                                <dt class="col-sm-3">Created</dt>
                                <dd class="col-sm-9"><?php echo e(\Illuminate\Support\Carbon::parse($assetRequest->created_at)->format('d M Y H:i')); ?></dd>

                                <dt class="col-sm-3">Last Updated</dt>
                                <dd class="col-sm-9"><?php echo e(\Illuminate\Support\Carbon::parse($assetRequest->updated_at)->format('d M Y H:i')); ?></dd>

                                <?php if($assetRequest->approved_by): ?>
                                    <dt class="col-sm-3">Approved By</dt>
                                    <dd class="col-sm-9">
                                        <strong><?php echo e($assetRequest->approvedBy->name); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e(\Illuminate\Support\Carbon::parse($assetRequest->approved_at)->format('d M Y H:i')); ?></small>
                                    </dd>

                                    <?php if($assetRequest->approval_notes): ?>
                                        <dt class="col-sm-3">Approval Notes</dt>
                                        <dd class="col-sm-9">
                                            <div class="alert alert-info mb-0">
                                                <?php echo e($assetRequest->approval_notes); ?>

                                            </div>
                                        </dd>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if($assetRequest->fulfilled_asset_id): ?>
                                    <dt class="col-sm-3">Fulfilled Asset</dt>
                                    <dd class="col-sm-9">
                                        <a href="<?php echo e(route('assets.show', $assetRequest->fulfilledAsset->id)); ?>" target="_blank">
                                            <?php echo e($assetRequest->fulfilledAsset->asset_tag ?? 'Asset #' . $assetRequest->fulfilledAsset->id); ?>

                                        </a>
                                        <br>
                                        <small class="text-muted"><?php echo e(\Illuminate\Support\Carbon::parse($assetRequest->fulfilled_at)->format('d M Y H:i')); ?></small>
                                    </dd>
                                <?php endif; ?>
                            </dl>
                        </div>

                        <div class="col-md-4">
                            <h4 class="mb-3">Justification</h4>
                            <div class="card border-left-primary">
                                <div class="card-body">
                                    <?php if($assetRequest->justification): ?>
                                        <p><?php echo e($assetRequest->justification); ?></p>
                                    <?php else: ?>
                                        <p class="text-muted"><em>No justification provided</em></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if(auth()->guard()->check()): ?>
                                <h4 class="mt-4 mb-3">
                                    <i class="fa fa-cogs"></i> Admin Actions
                                </h4>
                                <?php if($assetRequest->status === 'pending'): ?>
                                    <div class="btn-group-vertical w-100" role="group">
                                        <button class="btn btn-success text-left" data-toggle="modal" data-target="#approveModal">
                                            <i class="fa fa-check"></i> Approve Request
                                        </button>
                                        <button class="btn btn-danger text-left" data-toggle="modal" data-target="#rejectModal">
                                            <i class="fa fa-times"></i> Reject Request
                                        </button>
                                    </div>
                                <?php elseif($assetRequest->status === 'approved'): ?>
                                    <button class="btn btn-primary w-100" data-toggle="modal" data-target="#fulfillModal">
                                        <i class="fa fa-check-circle"></i> Mark as Fulfilled
                                    </button>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <small>No admin actions available for <?php echo e($assetRequest->status); ?> requests</small>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <small><a href="<?php echo e(route('login')); ?>">Please log in</a> to perform admin actions</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white">Approve Request</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="<?php echo e(route('asset-requests.approve', $assetRequest->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="admin_notes">Approval Notes (Optional)</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4"></textarea>
                            <small class="text-muted">Add any notes about this approval</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white">Reject Request</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="<?php echo e(route('asset-requests.reject', $assetRequest->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="reject_notes">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject_notes" name="admin_notes" rows="4" required></textarea>
                            <small class="text-muted">Please explain why this request is being rejected</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fulfill Modal -->
    <div class="modal fade" id="fulfillModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white">Mark as Fulfilled</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="<?php echo e(route('asset-requests.fulfill', $assetRequest->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fulfillment_notes">Fulfillment Notes (Optional)</label>
                            <textarea class="form-control" id="fulfillment_notes" name="fulfillment_notes" rows="4"></textarea>
                            <small class="text-muted">Add notes about how this request was fulfilled (e.g., asset tag)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Mark as Fulfilled</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\asset-requests\show.blade.php ENDPATH**/ ?>