

<?php
// Helper function to format minutes to human-readable format
if (!function_exists('formatMinutesToHumanReadable')) {
    function formatMinutesToHumanReadable($minutes) {
        if ($minutes < 60) {
            return $minutes . ' min';
        } elseif ($minutes < 1440) {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            return $hours . 'h' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . 'm' : '');
        } else {
            $days = floor($minutes / 1440);
            $remainingHours = floor(($minutes % 1440) / 60);
            return $days . 'd' . ($remainingHours > 0 ? ' ' . $remainingHours . 'h' : '');
        }
    }
}
?>

<?php $__env->startSection('main-content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-clock"></i> SLA Policies Management
                        </h3>
                        <div>
                            <a href="<?php echo e(route('sla.dashboard')); ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-line"></i> SLA Dashboard
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\SlaPolicy::class)): ?>
                                <a href="<?php echo e(route('sla.create')); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Create SLA Policy
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="20%">Policy Name</th>
                                    <th width="15%">Priority</th>
                                    <th width="15%">Response Time</th>
                                    <th width="15%">Resolution Time</th>
                                    <th width="10%">Business Hours</th>
                                    <th width="10%">Status</th>
                                    <th width="10%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $policies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($policy->id); ?></td>
                                        <td>
                                            <strong><?php echo e($policy->name); ?></strong>
                                            <?php if($policy->description): ?>
                                                <br><small class="text-muted"><?php echo e(\Illuminate\Support\Str::limit($policy->description, 50)); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($policy->priority): ?>
                                                <span class="badge badge-<?php echo e($policy->priority->color ?? 'secondary'); ?>">
                                                    <?php echo e($policy->priority->name); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">No Priority</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-reply text-info"></i> 
                                            <?php echo e($policy->response_time); ?> minutes
                                            <br><small class="text-muted">(<?php echo e(formatMinutesToHumanReadable($policy->response_time)); ?>)</small>
                                        </td>
                                        <td>
                                            <i class="fas fa-check-circle text-success"></i> 
                                            <?php echo e($policy->resolution_time); ?> minutes
                                            <br><small class="text-muted">(<?php echo e(formatMinutesToHumanReadable($policy->resolution_time)); ?>)</small>
                                        </td>
                                        <td>
                                            <?php if($policy->business_hours_only): ?>
                                                <span class="badge badge-info">
                                                    <i class="fas fa-business-time"></i> Business Hours
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> 24/7
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-toggle-status <?php echo e($policy->is_active ? 'btn-success' : 'btn-secondary'); ?>"
                                                    onclick="toggleStatus(<?php echo e($policy->id); ?>)"
                                                    data-id="<?php echo e($policy->id); ?>">
                                                <i class="fas fa-<?php echo e($policy->is_active ? 'check' : 'times'); ?>"></i>
                                                <?php echo e($policy->is_active ? 'Active' : 'Inactive'); ?>

                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view', $policy)): ?>
                                                    <a href="<?php echo e(route('sla.show', $policy->id)); ?>" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $policy)): ?>
                                                    <a href="<?php echo e(route('sla.edit', $policy->id)); ?>" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $policy)): ?>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="deletePolicy(<?php echo e($policy->id); ?>)"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No SLA policies found.</p>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\SlaPolicy::class)): ?>
                                                <a href="<?php echo e(route('sla.create')); ?>" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create First Policy
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if($policies->hasPages()): ?>
                        <div class="mt-3">
                            <?php echo e($policies->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="delete-form" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function toggleStatus(policyId) {
    if (confirm('Are you sure you want to toggle the status of this SLA policy?')) {
        $.ajax({
            url: `/sla/${policyId}/toggle-active`,
            type: 'POST',
            data: {
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message || 'Failed to toggle status');
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while toggling the status');
            }
        });
    }
}

function deletePolicy(policyId) {
    if (confirm('Are you sure you want to delete this SLA policy? This action cannot be undone.')) {
        const form = document.getElementById('delete-form');
        form.action = `/sla/${policyId}`;
        form.submit();
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/sla/index.blade.php ENDPATH**/ ?>