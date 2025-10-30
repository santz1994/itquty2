

<?php $__env->startSection('main-content'); ?>




<?php echo $__env->make('components.page-header', [
    'title' => 'Edit Asset Request',
    'subtitle' => 'Modify asset request details',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Asset Requests', 'url' => route('asset-requests.index')],
        ['label' => 'Edit Request #'.$assetRequest->id]
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">

    
    <div class="alert metadata-alert">
        <i class="fa fa-info-circle"></i> <strong>Request #<?php echo e($assetRequest->request_number ?? $assetRequest->id); ?></strong>
        <span class="pull-right">
            <small>
                Created: <?php echo e($assetRequest->created_at->format('d M Y, h:i A')); ?>

                <?php if($assetRequest->updated_at && $assetRequest->updated_at != $assetRequest->created_at): ?>
                    | Last Updated: <?php echo e($assetRequest->updated_at->format('d M Y, h:i A')); ?>

                <?php endif; ?>
            </small>
        </span>
    </div>

    <div class="row">
        
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-edit"></i> Edit Request Details</h3>
                </div>
                <div class="box-body">

                    
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fa fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fa fa-exclamation-circle"></i> <strong>Validation errors:</strong>
                            <ul style="margin-bottom: 0; margin-top: 5px;">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if($assetRequest->status !== 'pending'): ?>
                        <div class="alert alert-info">
                            <i class="fa fa-lock"></i> <strong>Note:</strong> This request cannot be edited because it has already been <?php echo e($assetRequest->status); ?>.
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('asset-requests.update', $assetRequest->id)); ?>" method="POST" id="asset-request-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        
                        <fieldset>
                            <legend><span class="form-section-icon"><i class="fa fa-box"></i></span> Asset Details</legend>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Asset Name / Title <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                                            <input type="text" name="title" id="title" 
                                                   class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   value="<?php echo e(old('title', $assetRequest->title)); ?>" 
                                                   placeholder="e.g., Dell Latitude 7420 Laptop" 
                                                   <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : 'required'); ?>>
                                        </div>
                                        <small class="help-text">Enter the name or model of the asset you need</small>
                                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="asset_type_id">Asset Type <span class="text-danger">*</span></label>
                                        <select class="form-control <?php $__errorArgs = ['asset_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                id="asset_type_id" name="asset_type_id" 
                                                <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : 'required'); ?>>
                                            <option value="">Select Type</option>
                                            <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($type->id); ?>" <?php echo e(old('asset_type_id', $assetRequest->asset_type_id) == $type->id ? 'selected' : ''); ?>>
                                                    <?php echo e($type->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="help-text">Category of asset</small>
                                        <?php $__errorArgs = ['asset_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="requested_quantity">Quantity</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                                            <input type="number" name="requested_quantity" id="requested_quantity" 
                                                   class="form-control <?php $__errorArgs = ['requested_quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   value="<?php echo e(old('requested_quantity', $assetRequest->requested_quantity ?? 1)); ?>" 
                                                   min="1"
                                                   <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : ''); ?>>
                                        </div>
                                        <small class="help-text">How many units needed</small>
                                        <?php $__errorArgs = ['requested_quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="unit">Unit</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-cube"></i></span>
                                            <input type="text" name="unit" id="unit" 
                                                   class="form-control <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   value="<?php echo e(old('unit', $assetRequest->unit)); ?>"
                                                   placeholder="e.g., pcs, set, unit"
                                                   <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : ''); ?>>
                                        </div>
                                        <small class="help-text">Unit of measurement</small>
                                        <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="priority">Priority</label>
                                        <select class="form-control <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                id="priority" name="priority"
                                                <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : ''); ?>>
                                            <option value="">Select Priority</option>
                                            <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($priority); ?>" <?php echo e(old('priority', $assetRequest->priority) == $priority ? 'selected' : ''); ?>>
                                                    <?php echo e(ucfirst($priority)); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <small class="help-text">Urgency level</small>
                                        <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="needed_date">Needed By Date</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="date" name="needed_date" id="needed_date" 
                                                   class="form-control <?php $__errorArgs = ['needed_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                   value="<?php echo e(old('needed_date', $assetRequest->needed_date ? $assetRequest->needed_date->format('Y-m-d') : '')); ?>"
                                                   <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : ''); ?>>
                                        </div>
                                        <small class="help-text">When do you need this asset?</small>
                                        <?php $__errorArgs = ['needed_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        
                        <fieldset>
                            <legend><span class="form-section-icon"><i class="fa fa-file-alt"></i></span> Justification</legend>
                            
                            <div class="form-group">
                                <label for="justification">Business Justification <span class="text-danger">*</span></label>
                                <textarea class="form-control <?php $__errorArgs = ['justification'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="justification" name="justification" rows="6" 
                                          <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : 'required'); ?>><?php echo e(old('justification', $assetRequest->justification)); ?></textarea>
                                <small class="help-text">Explain why this asset is needed and its business impact</small>
                                <?php $__errorArgs = ['justification'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="form-group">
                                <label for="notes">Additional Notes (Optional)</label>
                                <textarea class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                          id="notes" name="notes" rows="3"
                                          <?php echo e($assetRequest->status !== 'pending' ? 'disabled' : ''); ?>><?php echo e(old('notes', $assetRequest->notes)); ?></textarea>
                                <small class="help-text">Any additional information or special requirements</small>
                                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </fieldset>

                        
                        <?php if($assetRequest->status === 'pending'): ?>
                            <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-save"></i> <b>Update Request</b>
                                </button>
                                <a href="<?php echo e(route('asset-requests.index')); ?>" class="btn btn-default btn-lg">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
                                <a href="<?php echo e(route('asset-requests.index')); ?>" class="btn btn-default btn-lg">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                                <a href="<?php echo e(route('asset-requests.show', $assetRequest->id)); ?>" class="btn btn-info btn-lg">
                                    <i class="fa fa-eye"></i> View Details
                                </a>
                            </div>
                        <?php endif; ?>

                    </form>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            
            
            <div class="box box-<?php echo e($assetRequest->status === 'approved' ? 'success' : ($assetRequest->status === 'rejected' ? 'danger' : 'warning')); ?>">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle"></i> Request Status</h3>
                </div>
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt>Status:</dt>
                        <dd>
                            <?php if($assetRequest->status === 'fulfilled'): ?>
                                <span class="label label-primary"><i class="fa fa-check-double"></i> Fulfilled</span>
                            <?php elseif($assetRequest->status === 'approved'): ?>
                                <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                            <?php elseif($assetRequest->status === 'rejected'): ?>
                                <span class="label label-danger"><i class="fa fa-times"></i> Rejected</span>
                            <?php else: ?>
                                <span class="label label-warning"><i class="fa fa-clock"></i> Pending</span>
                            <?php endif; ?>
                        </dd>
                        
                        <dt>Requested By:</dt>
                        <dd><?php echo e($assetRequest->requestedBy->name ?? 'N/A'); ?></dd>
                        
                        <dt>Division:</dt>
                        <dd><?php echo e($assetRequest->requestedBy->division ?? 'N/A'); ?></dd>
                        
                        <dt>Created:</dt>
                        <dd><?php echo e($assetRequest->created_at->format('d M Y')); ?></dd>
                    </dl>
                </div>
            </div>

            
            <?php if($assetRequest->status === 'pending'): ?>
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-lightbulb"></i> Edit Tips</h3>
                </div>
                <div class="box-body">
                    <ul style="margin-left: 20px;">
                        <li><i class="fa fa-exclamation-triangle text-warning"></i> Only pending requests can be edited</li>
                        <li><i class="fa fa-info-circle text-info"></i> Changes will reset approval workflow</li>
                        <li><i class="fa fa-check text-success"></i> Review all details before saving</li>
                        <li><i class="fa fa-clock text-muted"></i> Notify your manager of changes</li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="box-body">
                    <a href="<?php echo e(route('asset-requests.index')); ?>" class="btn btn-default btn-block">
                        <i class="fa fa-list"></i> Back to List
                    </a>
                    <a href="<?php echo e(route('asset-requests.show', $assetRequest->id)); ?>" class="btn btn-info btn-block">
                        <i class="fa fa-eye"></i> View Full Details
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Form validation (only if editable)
    <?php if($assetRequest->status === 'pending'): ?>
    $('#asset-request-form').on('submit', function(e) {
        if ($('#title').val().trim() === '') {
            alert('Please enter the asset name/title');
            $('#title').focus();
            return false;
        }
        
        if ($('#asset_type_id').val() === '') {
            alert('Please select an asset type');
            $('#asset_type_id').focus();
            return false;
        }
        
        if ($('#justification').val().trim().length < 20) {
            alert('Please provide a detailed justification (minimum 20 characters)');
            $('#justification').focus();
            return false;
        }
        
        return true;
    });
    <?php endif; ?>

    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
<?php $__env->stopPush(); ?>
                                    <label for="updated_at">Terakhir Diperbarui</label>
                                    <input type="text" class="form-control" value="<?php echo e(optional($assetRequest->updated_at)->format('d M Y H:i')); ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Request
                            </button>
                            <a href="<?php echo e(route('asset-requests.show', $assetRequest->id)); ?>" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/asset-requests/edit.blade.php ENDPATH**/ ?>