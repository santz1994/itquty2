

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i> Edit SLA Policy
                        </h3>
                        <a href="<?php echo e(route('sla.index')); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <form action="<?php echo e(route('sla.update', $policy->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="card-body">
                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5><i class="fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                                <ul class="mb-0">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        Policy Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="name" 
                                           name="name" 
                                           value="<?php echo e(old('name', $policy->name)); ?>" 
                                           placeholder="e.g., Urgent Priority SLA"
                                           required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">
                                        A descriptive name for this SLA policy
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority_id">
                                        Ticket Priority <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control <?php $__errorArgs = ['priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="priority_id" 
                                            name="priority_id" 
                                            required>
                                        <option value="">Select Priority</option>
                                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($priority->id); ?>" 
                                                    <?php echo e(old('priority_id', $policy->priority_id) == $priority->id ? 'selected' : ''); ?>>
                                                <?php echo e($priority->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">
                                        This policy will apply to tickets with this priority
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Describe when and how this SLA policy should be used"><?php echo e(old('description', $policy->description)); ?></textarea>
                                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <!-- SLA Timeframes -->
                        <h5 class="mt-4 mb-3">
                            <i class="fas fa-clock"></i> SLA Timeframes
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="response_time">
                                        First Response Time (minutes) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['response_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="response_time" 
                                           name="response_time" 
                                           value="<?php echo e(old('response_time', $policy->response_time)); ?>" 
                                           min="1"
                                           placeholder="e.g., 60"
                                           required>
                                    <?php $__errorArgs = ['response_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">
                                        Time allowed for first response (e.g., 60 = 1 hour, 1440 = 1 day)
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="resolution_time">
                                        Resolution Time (minutes) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['resolution_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="resolution_time" 
                                           name="resolution_time" 
                                           value="<?php echo e(old('resolution_time', $policy->resolution_time)); ?>" 
                                           min="1"
                                           placeholder="e.g., 240"
                                           required>
                                    <?php $__errorArgs = ['resolution_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">
                                        Time allowed to fully resolve the ticket (e.g., 240 = 4 hours, 4320 = 3 days)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="business_hours_only" 
                                               name="business_hours_only" 
                                               value="1"
                                               <?php echo e(old('business_hours_only', $policy->business_hours_only) ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="business_hours_only">
                                            Calculate SLA during business hours only
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        <i class="fas fa-info-circle"></i> 
                                        Business hours: Monday to Friday, 8:00 AM - 5:00 PM. 
                                        When checked, SLA calculations will exclude weekends and after-hours.
                                        Uncheck for 24/7 SLA calculation.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Escalation Settings -->
                        <h5 class="mt-4 mb-3">
                            <i class="fas fa-exclamation-triangle"></i> Escalation Settings
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="escalation_time">
                                        Escalation Time (minutes)
                                    </label>
                                    <input type="number" 
                                           class="form-control <?php $__errorArgs = ['escalation_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="escalation_time" 
                                           name="escalation_time" 
                                           value="<?php echo e(old('escalation_time', $policy->escalation_time)); ?>" 
                                           min="1"
                                           placeholder="e.g., 120">
                                    <?php $__errorArgs = ['escalation_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">
                                        Time before ticket should be escalated. Leave empty to disable auto-escalation.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="escalate_to_user_id">
                                        Escalate To User
                                    </label>
                                    <select class="form-control <?php $__errorArgs = ['escalate_to_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="escalate_to_user_id" 
                                            name="escalate_to_user_id">
                                        <option value="">Select User (Optional)</option>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($user->id); ?>" 
                                                    <?php echo e(old('escalate_to_user_id', $policy->escalate_to_user_id) == $user->id ? 'selected' : ''); ?>>
                                                <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['escalate_to_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <small class="form-text text-muted">
                                        User to receive escalated tickets. Leave empty if escalation is not needed.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               <?php echo e(old('is_active', $policy->is_active) ? 'checked' : ''); ?>>
                                        <label class="custom-control-label" for="is_active">
                                            <strong>Active Policy</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Only active policies will be applied to tickets
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Time Presets -->
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-lightbulb"></i> Quick Time Reference:</h6>
                            <ul class="mb-0">
                                <li><strong>1 hour</strong> = 60 minutes</li>
                                <li><strong>4 hours</strong> = 240 minutes</li>
                                <li><strong>1 day</strong> = 1440 minutes</li>
                                <li><strong>3 days</strong> = 4320 minutes</li>
                                <li><strong>1 week</strong> = 10080 minutes</li>
                            </ul>
                        </div>

                        <!-- Metadata -->
                        <div class="alert alert-secondary mt-3">
                            <small class="text-muted">
                                <strong>Created:</strong> <?php echo e($policy->created_at->format('Y-m-d H:i:s')); ?>

                                <?php if($policy->updated_at): ?>
                                    | <strong>Last Updated:</strong> <?php echo e($policy->updated_at->format('Y-m-d H:i:s')); ?>

                                <?php endif; ?>
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update SLA Policy
                        </button>
                        <a href="<?php echo e(route('sla.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $policy)): ?>
                            <button type="button" 
                                    class="btn btn-danger float-right" 
                                    onclick="deletePolicy()">
                                <i class="fas fa-trash"></i> Delete Policy
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="delete-form" action="<?php echo e(route('sla.destroy', $policy->id)); ?>" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function deletePolicy() {
    if (confirm('Are you sure you want to delete this SLA policy? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\sla\edit.blade.php ENDPATH**/ ?>