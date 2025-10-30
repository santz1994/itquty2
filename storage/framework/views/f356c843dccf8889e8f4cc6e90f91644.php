

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', [
    'title' => 'Conflict Detail',
    'subtitle' => 'Review and resolve this conflict',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Master Data', 'url' => route('masterdata.index')],
        ['label' => 'Imports', 'url' => route('masterdata.imports')],
        ['label' => 'Conflicts', 'url' => route('imports.conflicts.index', $import->import_id)],
        ['label' => 'Detail']
    ],
    'actions' => '<a href="'.route('imports.conflicts.index', $import->import_id).'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to Conflicts
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-md-8">
        <!-- Conflict Information -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-exclamation-circle"></i> Conflict Information
                </h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="dl-horizontal">
                            <dt>Import ID:</dt>
                            <dd><code><?php echo e($import->import_id); ?></code></dd>
                            
                            <dt>Row Number:</dt>
                            <dd><?php echo e($conflict->row_number); ?></dd>
                            
                            <dt>Conflict Type:</dt>
                            <dd>
                                <span class="badge" style="background-color: <?php switch($conflict->conflict_type):
                                    case ('duplicate_key'): ?> #DD4B39 <?php break; ?>
                                    <?php case ('duplicate_record'): ?> #F39C12 <?php break; ?>
                                    <?php case ('foreign_key_not_found'): ?> #3498DB <?php break; ?>
                                    <?php case ('invalid_data'): ?> #9B59B6 <?php break; ?>
                                    <?php case ('business_rule_violation'): ?> #E74C3C <?php break; ?>
                                    <?php default: ?> #95A5A6
                                <?php endswitch; ?>">
                                    <?php echo e($conflict->getConflictTypeLabel()); ?>

                                </span>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl class="dl-horizontal">
                            <dt>Status:</dt>
                            <dd>
                                <?php if($conflict->isResolved()): ?>
                                    <span class="label label-success">Resolved</span>
                                <?php else: ?>
                                    <span class="label label-danger">Unresolved</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt>Suggested Resolution:</dt>
                            <dd>
                                <span class="label label-info">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $conflict->suggested_resolution))); ?>

                                </span>
                            </dd>
                            
                            <dt>Created At:</dt>
                            <dd><?php echo e($conflict->created_at->format('M d, Y h:i A')); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Record Data -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-file-o"></i> New Record Data
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <?php if($conflict->new_record_data): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="30%">Field</th>
                                    <th width="70%">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $conflict->new_record_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong><?php echo e(ucfirst(str_replace('_', ' ', $field))); ?></strong></td>
                                        <td>
                                            <?php if(is_array($value)): ?>
                                                <code><?php echo e(json_encode($value)); ?></code>
                                            <?php elseif(is_null($value)): ?>
                                                <span class="text-muted">NULL</span>
                                            <?php else: ?>
                                                <?php echo e($value); ?>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No record data available</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Existing Record Information -->
        <?php if($conflict->existing_record_id): ?>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-database"></i> Existing Record (#<?php echo e($conflict->existing_record_id); ?>)
                    </h3>
                </div>
                <div class="box-body">
                    <p class="text-muted">Existing record ID: <strong><?php echo e($conflict->existing_record_id); ?></strong></p>
                    <p class="text-muted" style="font-size: 12px;">The imported data conflicts with this existing record in the system.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Related Conflicts -->
        <?php if($relatedConflicts->count() > 0): ?>
            <div class="box box-secondary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-link"></i> Similar Conflicts (<?php echo e($relatedConflicts->count()); ?>)
                    </h3>
                </div>
                <div class="box-body">
                    <div class="list-group">
                        <?php $__currentLoopData = $relatedConflicts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('imports.conflicts.show', [$import->import_id, $related->id])); ?>" 
                               class="list-group-item">
                                <h4 class="list-group-item-heading">
                                    Row <?php echo e($related->row_number); ?>

                                    <?php if($related->id === $conflict->id): ?>
                                        <span class="label label-primary">Current</span>
                                    <?php endif; ?>
                                </h4>
                                <p class="list-group-item-text">
                                    <?php echo e($related->getConflictTypeLabel()); ?>

                                </p>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Resolution Panel -->
    <div class="col-md-4">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-check-circle"></i> Resolve Conflict
                </h3>
            </div>
            <div class="box-body">
                <?php if($conflict->isResolved()): ?>
                    <div class="alert alert-success">
                        <strong>Already Resolved!</strong><br>
                        Resolution: <strong><?php echo e($conflict->getResolutionLabel()); ?></strong><br>
                        Resolved At: <strong><?php echo e($conflict->updated_at->format('M d, Y h:i A')); ?></strong>
                    </div>
                <?php else: ?>
                    <form id="resolution-form">
                        <?php echo e(csrf_field()); ?>

                        
                        <div class="form-group">
                            <label for="resolution">Select Resolution Type:</label>
                            <select id="resolution" name="resolution" class="form-control" required>
                                <option value="">-- Choose Resolution --</option>
                                <option value="skip" <?php if($conflict->suggested_resolution === 'skip'): ?> selected <?php endif; ?>>
                                    <i class="fa fa-forward"></i> Skip Row
                                </option>
                                <option value="create_new" <?php if($conflict->suggested_resolution === 'create_new'): ?> selected <?php endif; ?>>
                                    <i class="fa fa-plus"></i> Create New Record
                                </option>
                                <option value="update_existing" <?php if($conflict->suggested_resolution === 'update_existing'): ?> selected <?php endif; ?>>
                                    <i class="fa fa-pencil"></i> Update Existing
                                </option>
                                <option value="merge" <?php if($conflict->suggested_resolution === 'merge'): ?> selected <?php endif; ?>>
                                    <i class="fa fa-compress"></i> Merge Records
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                <strong>Suggested:</strong> <?php echo e($conflict->getResolutionLabel()); ?>

                            </small>
                        </div>

                        <div class="form-group">
                            <label for="details">Additional Notes (Optional):</label>
                            <textarea id="details" name="details" class="form-control" rows="3" 
                                      placeholder="Enter any additional information about this resolution..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fa fa-check"></i> Resolve This Conflict
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Suggested Resolutions Guide -->
                <hr>
                <h5><i class="fa fa-lightbulb-o"></i> Resolution Guide</h5>
                <div style="font-size: 12px;">
                    <div class="form-group">
                        <strong class="text-info">Skip Row</strong>
                        <p class="text-muted">Don't import this row. Use when the data is invalid or unwanted.</p>
                    </div>
                    <div class="form-group">
                        <strong class="text-success">Create New</strong>
                        <p class="text-muted">Create a new record with the imported data instead of updating existing.</p>
                    </div>
                    <div class="form-group">
                        <strong class="text-warning">Update Existing</strong>
                        <p class="text-muted">Update the existing record with the new imported data.</p>
                    </div>
                    <div class="form-group">
                        <strong class="text-primary">Merge</strong>
                        <p class="text-muted">Intelligently merge existing and new data, keeping the best of both.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    $('#resolution-form').on('submit', function(e) {
        e.preventDefault();

        const resolution = $('#resolution').val();
        const details = $('#details').val();

        if (!resolution) {
            toastr.error('Please select a resolution type', 'Error');
            return;
        }

        $.ajax({
            url: '<?php echo e(route("imports.conflicts.resolve", [$import->import_id, $conflict->id])); ?>',
            method: 'POST',
            data: {
                resolution: resolution,
                details: details,
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                toastr.success('Conflict resolved successfully', 'Success');
                setTimeout(function() {
                    window.location.href = '<?php echo e(route("imports.conflicts.index", $import->import_id)); ?>';
                }, 1500);
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Failed to resolve conflict';
                toastr.error(error, 'Error');
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\imports\conflicts\show.blade.php ENDPATH**/ ?>