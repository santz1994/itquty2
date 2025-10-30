

<?php $__env->startSection('page_title'); ?>
    <?php echo e($pageTitle); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    View record #<?php echo e($id); ?> in <?php echo e($tableName); ?> table
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-eye"></i> Record Details #<?php echo e($id); ?>

                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('admin.database.edit', [$tableName, $id])); ?>" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRecord()">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                    <a href="<?php echo e(route('admin.database.table', $tableName)); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Table
                    </a>
                </div>
            </div>
            
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td style="width: 30%; font-weight: bold; background-color: #f5f5f5;">
                                    <?php echo e(ucwords(str_replace('_', ' ', $column->column_name))); ?>

                                    <br><small class="text-muted"><?php echo e($column->type); ?></small>
                                </td>
                                <td>
                                    <?php
                                        $value = $record->{$column->column_name} ?? '';
                                        $displayValue = $value;
                                        
                                        // Format display based on column type
                                        if ($value === null) {
                                            $displayValue = '<span class="text-muted">NULL</span>';
                                        } elseif ($value === '') {
                                            $displayValue = '<span class="text-muted">(Empty)</span>';
                                        } elseif (strpos($column->type, 'tinyint(1)') !== false) {
                                            $displayValue = $value ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                                        } elseif (strpos($column->type, 'datetime') !== false || strpos($column->type, 'timestamp') !== false) {
                                            if ($value && $value !== '0000-00-00 00:00:00') {
                                                try {
                                                    $displayValue = \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') . 
                                                                   '<br><small class="text-muted">' . \Carbon\Carbon::parse($value)->diffForHumans() . '</small>';
                                                } catch (Exception $e) {
                                                    $displayValue = $value;
                                                }
                                            } else {
                                                $displayValue = '<span class="text-muted">Not set</span>';
                                            }
                                        } elseif (strpos($column->type, 'date') !== false) {
                                            if ($value && $value !== '0000-00-00') {
                                                try {
                                                    $displayValue = \Carbon\Carbon::parse($value)->format('Y-m-d');
                                                } catch (Exception $e) {
                                                    $displayValue = $value;
                                                }
                                            } else {
                                                $displayValue = '<span class="text-muted">Not set</span>';
                                            }
                                        } elseif (strpos($column->type, 'text') !== false || strpos($column->type, 'longtext') !== false) {
                                            if (strlen($value) > 200) {
                                                $displayValue = substr($value, 0, 200) . '...';
                                                $displayValue .= '<br><small class="text-muted">(' . strlen($value) . ' characters)</small>';
                                            }
                                        } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                            $displayValue = '<a href="mailto:' . $value . '">' . $value . '</a>';
                                        } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                                            $displayValue = '<a href="' . $value . '" target="_blank">' . $value . ' <i class="fa fa-external-link"></i></a>';
                                        } elseif (is_numeric($value) && strlen($value) > 10) {
                                            // Format large numbers with separators
                                            $displayValue = number_format($value);
                                        }
                                        
                                        // Check for foreign key relationships
                                        if (str_ends_with($column->column_name, '_id') && $value && $value !== '0') {
                                            $relatedTable = str_replace('_id', 's', $column->column_name);
                                            if ($relatedTable === 'users') {
                                                $relatedTable = 'users';
                                            }
                                            $displayValue .= '<br><small class="text-info">
                                                <i class="fa fa-link"></i> May reference ' . $relatedTable . ' table
                                            </small>';
                                        }
                                    ?>
                                    
                                    <?php echo $displayValue; ?>

                                    
                                    <?php if($column->nullable && $value === null): ?>
                                        <br><small class="text-warning"><i class="fa fa-info-circle"></i> Nullable field</small>
                                    <?php endif; ?>
                                    
                                    <?php if($column->auto_increment): ?>
                                        <br><small class="text-success"><i class="fa fa-cog"></i> Auto-increment</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="box-footer">
                <div class="row">
                    <div class="col-md-6">
                        <a href="<?php echo e(route('admin.database.edit', [$tableName, $id])); ?>" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit Record
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteRecord()">
                            <i class="fa fa-trash"></i> Delete Record
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="<?php echo e(route('admin.database.create', $tableName)); ?>" class="btn btn-success">
                            <i class="fa fa-plus"></i> Create New Record
                        </a>
                        <a href="<?php echo e(route('admin.database.table', $tableName)); ?>" class="btn btn-default">
                            <i class="fa fa-list"></i> Back to Table
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-exclamation-triangle text-danger"></i> Confirm Delete
                </h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record from the <strong><?php echo e($tableName); ?></strong> table?</p>
                <p class="text-danger">
                    <i class="fa fa-warning"></i>
                    <strong>This action cannot be undone!</strong>
                </p>
                <p>Record ID: <code>#<?php echo e($id); ?></code></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <form id="deleteForm" method="POST" action="<?php echo e(route('admin.database.destroy', [$tableName, $id])); ?>" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Yes, Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<script>
function deleteRecord() {
    $('#deleteModal').modal('show');
}
</script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\database\show.blade.php ENDPATH**/ ?>