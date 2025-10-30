

<?php $__env->startSection('main-content'); ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Movement History - <?php echo e($asset->asset_tag ?? '—'); ?></h3>
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

        <!-- Assign to User Section -->
        <div class="col-md-4">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user"></i> Assign to User</h3>
                </div>
                <div class="box-body">
                    <div class="alert alert-info">
                        <strong>Current Assignment:</strong><br>
                        <?php echo e(optional($asset->assignedTo)->name ?? 'Unassigned'); ?>

                    </div>

                    <form method="POST" action="<?php echo e(route('assets.assign', $asset->id)); ?>" id="assignForm">
                        <?php echo e(csrf_field()); ?>

                        <div class="form-group">
                            <label for="user_id">Assign to User</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">-- Select User --</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e($asset->assigned_to == $user->id ? 'selected' : ''); ?>>
                                        <?php echo e($user->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add assignment notes..."></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-save"></i> Assign Asset
                            </button>
                            <?php if($asset->assigned_to): ?>
                                <button type="button" class="btn btn-warning btn-block" onclick="unassignAsset()">
                                    <i class="fa fa-trash"></i> Unassign
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function unassignAsset() {
            if (confirm('Are you sure you want to unassign this asset?')) {
                fetch('<?php echo e(route("assets.unassign", $asset->id)); ?>', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to unassign asset'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while unassigning the asset');
                });
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\assets\movements.blade.php ENDPATH**/ ?>