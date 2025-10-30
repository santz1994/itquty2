

<?php $__env->startSection('page_title'); ?>
    <?php echo e($pageTitle); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Manage records in <?php echo e($tableName); ?> table
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <!-- Table Info -->
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-table"></i> Table: <?php echo e($tableName); ?>

                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('admin.database.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Database
                    </a>
                    <a href="<?php echo e(route('admin.database.create', $tableName)); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Add Record
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Records</span>
                                <span class="info-box-number"><?php echo e(number_format($tableStats->row_count ?? 0)); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-columns"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Columns</span>
                                <span class="info-box-number"><?php echo e(count($columns)); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-hdd-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Data Size</span>
                                <span class="info-box-number"><?php echo e(number_format(($tableStats->data_size ?? 0) / 1024, 2)); ?> KB</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-key"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Indexes</span>
                                <span class="info-box-number"><?php echo e(count($indexes)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-search"></i> Search Records
                </h3>
            </div>
            <div class="box-body">
                <form method="GET" action="<?php echo e(route('admin.database.table', $tableName)); ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="search">Search</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       value="<?php echo e($search); ?>" placeholder="Search in text fields...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="per_page">Records per page</label>
                                <select name="per_page" id="per_page" class="form-control">
                                    <option value="25" <?php echo e(request('per_page') == 25 ? 'selected' : ''); ?>>25</option>
                                    <option value="50" <?php echo e(request('per_page') == 50 ? 'selected' : ''); ?>>50</option>
                                    <option value="100" <?php echo e(request('per_page') == 100 ? 'selected' : ''); ?>>100</option>
                                    <option value="500" <?php echo e(request('per_page') == 500 ? 'selected' : ''); ?>>500</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Search
                                </button>
                                <a href="<?php echo e(route('admin.database.table', $tableName)); ?>" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Table Structure Tab -->
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#data-tab" data-toggle="tab">Data (<?php echo e($totalRecords); ?> records)</a></li>
                <li><a href="#structure-tab" data-toggle="tab">Structure</a></li>
                <li><a href="#indexes-tab" data-toggle="tab">Indexes</a></li>
            </ul>
            <div class="tab-content">
                <!-- Data Tab -->
                <div class="tab-pane active" id="data-tab">
                    <?php if($records->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th>
                                        <?php echo e($column->column_name); ?>

                                        <?php if($column->key === 'PRI'): ?>
                                            <i class="fa fa-key text-warning" title="Primary Key"></i>
                                        <?php endif; ?>
                                    </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td>
                                        <?php
                                            $value = $record->{$column->column_name} ?? null;
                                        ?>
                                        
                                        <?php if($value === null): ?>
                                            <em class="text-muted">NULL</em>
                                        <?php elseif(is_string($value) && strlen($value) > 50): ?>
                                            <span title="<?php echo e($value); ?>"><?php echo e(substr($value, 0, 50)); ?>...</span>
                                        <?php elseif($column->type === 'timestamp' || $column->type === 'datetime'): ?>
                                            <?php echo e($value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') : ''); ?>

                                        <?php else: ?>
                                            <?php echo e($value); ?>

                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if(isset($record->id)): ?>
                                            <a href="<?php echo e(route('admin.database.edit', [$tableName, $record->id])); ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="confirmDelete('<?php echo e($tableName); ?>', '<?php echo e($record->id); ?>')" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                            <?php else: ?>
                                            <em class="text-muted">No ID column</em>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="text-center">
                        <?php echo e($records->appends(request()->query())->links()); ?>

                    </div>
                    <?php else: ?>
                    <div class="text-center">
                        <p class="text-muted">No records found.</p>
                        <a href="<?php echo e(route('admin.database.create', $tableName)); ?>" class="btn btn-success">
                            <i class="fa fa-plus"></i> Add First Record
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Structure Tab -->
                <div class="tab-pane" id="structure-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Column</th>
                                    <th>Type</th>
                                    <th>Null</th>
                                    <th>Key</th>
                                    <th>Default</th>
                                    <th>Extra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($column->column_name); ?></strong></td>
                                    <td><code><?php echo e($column->type); ?></code></td>
                                    <td>
                                        <?php if($column->nullable): ?>
                                            <span class="label label-success">YES</span>
                                        <?php else: ?>
                                            <span class="label label-danger">NO</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($column->key): ?>
                                            <span class="label label-warning"><?php echo e($column->key); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($column->default !== null): ?>
                                            <code><?php echo e($column->default); ?></code>
                                        <?php else: ?>
                                            <em class="text-muted">NULL</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($column->extra): ?>
                                            <code><?php echo e($column->extra); ?></code>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Indexes Tab -->
                <div class="tab-pane" id="indexes-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Key Name</th>
                                    <th>Column Name</th>
                                    <th>Unique</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $indexes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($index->Key_name); ?></strong></td>
                                    <td><?php echo e($index->Column_name); ?></td>
                                    <td>
                                        <?php if($index->Non_unique == 0): ?>
                                            <span class="label label-success">YES</span>
                                        <?php else: ?>
                                            <span class="label label-default">NO</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?php echo e($index->Index_type); ?></code></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
                <p><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
<script>
function confirmDelete(tableName, recordId) {
    $('#deleteForm').attr('action', '/admin/database/' + tableName + '/' + recordId);
    $('#deleteModal').modal('show');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\database\table.blade.php ENDPATH**/ ?>