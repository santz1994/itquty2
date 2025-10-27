

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Asset Request #<?php echo e($assetRequest->id); ?></h3>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('asset-requests.update', $assetRequest->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="asset_type_id">Tipe Asset <span class="text-danger">*</span></label>
                                    <select class="form-control <?php $__errorArgs = ['asset_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="asset_type_id" name="asset_type_id" required>
                                        <option value="">Pilih Tipe Asset</option>
                                        <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type->id); ?>" 
                                                    <?php echo e(old('asset_type_id', $assetRequest->asset_type_id) == $type->id ? 'selected' : ''); ?>>
                                                <?php echo e($type->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['asset_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" class="form-control" id="status" value="<?php echo e(ucfirst($assetRequest->status)); ?>" disabled>
                                    <small class="form-text text-muted">Status hanya dapat diubah melalui proses approval/rejection</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="justification">Justification / Alasan <span class="text-danger">*</span></label>
                            <textarea class="form-control <?php $__errorArgs = ['justification'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="justification" name="justification" rows="6" required><?php echo e(old('justification', $assetRequest->justification)); ?></textarea>
                            <small class="form-text text-muted">
                                Jelaskan secara detail:
                                <ul>
                                    <li>Mengapa asset ini dibutuhkan?</li>
                                    <li>Untuk keperluan apa asset akan digunakan?</li>
                                    <li>Dampak jika tidak mendapatkan asset ini</li>
                                </ul>
                            </small>
                            <?php $__errorArgs = ['justification'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="requested_by">Diminta Oleh</label>
                                    <input type="text" class="form-control" value="<?php echo e($assetRequest->requestedBy->name ?? 'N/A'); ?>" disabled>
                                    <small class="form-text text-muted">Tidak dapat diubah</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="created_at">Tanggal Dibuat</label>
                                    <input type="text" class="form-control" value="<?php echo e($assetRequest->created_at->format('d M Y H:i')); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="updated_at">Terakhir Diperbarui</label>
                                    <input type="text" class="form-control" value="<?php echo e($assetRequest->updated_at->format('d M Y H:i')); ?>" disabled>
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