

<?php $__env->startSection('page_title'); ?>
    <?php echo e($pageTitle); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Add a new record to <?php echo e($tableName); ?> table
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-plus"></i> Add New Record
                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('admin.database.table', $tableName)); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Table
                    </a>
                </div>
            </div>
            
            <form method="POST" action="<?php echo e(route('admin.database.store', $tableName)); ?>">
                <?php echo csrf_field(); ?>
                
                <div class="box-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php $__currentLoopData = $editableColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="form-group <?php echo e($errors->has($column->column_name) ? 'has-error' : ''); ?>">
                        <label for="<?php echo e($column->column_name); ?>">
                            <?php echo e(ucwords(str_replace('_', ' ', $column->column_name))); ?>

                            <?php if(!$column->nullable && $column->default === null): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        
                        <?php
                            $inputType = 'text';
                            $inputClass = 'form-control';
                            $placeholder = '';
                            
                            // Determine input type based on column type
                            if (strpos($column->type, 'int') !== false) {
                                $inputType = 'number';
                            } elseif (strpos($column->type, 'decimal') !== false || strpos($column->type, 'float') !== false) {
                                $inputType = 'number';
                                $placeholder = 'step="0.01"';
                            } elseif (strpos($column->type, 'date') !== false && strpos($column->type, 'time') === false) {
                                $inputType = 'date';
                            } elseif (strpos($column->type, 'datetime') !== false || strpos($column->type, 'timestamp') !== false) {
                                $inputType = 'datetime-local';
                            } elseif (strpos($column->type, 'time') !== false) {
                                $inputType = 'time';
                            } elseif (strpos($column->type, 'email') !== false) {
                                $inputType = 'email';
                            } elseif (strpos($column->type, 'text') !== false || strpos($column->type, 'longtext') !== false) {
                                $inputType = 'textarea';
                            } elseif (strpos($column->type, 'tinyint(1)') !== false) {
                                $inputType = 'checkbox';
                            } elseif (strpos($column->type, 'enum') !== false) {
                                $inputType = 'select';
                                // Extract enum values
                                preg_match('/enum\((.*)\)/', $column->type, $matches);
                                $enumValues = [];
                                if (isset($matches[1])) {
                                    $enumValues = explode(',', $matches[1]);
                                    $enumValues = array_map(function($val) {
                                        return trim($val, "'\"");
                                    }, $enumValues);
                                }
                            }
                        ?>

                        <?php if($inputType === 'textarea'): ?>
                            <textarea name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                      class="<?php echo e($inputClass); ?>" rows="4"
                                      <?php echo e(!$column->nullable && $column->default === null ? 'required' : ''); ?>

                                      placeholder="Enter <?php echo e(strtolower(str_replace('_', ' ', $column->column_name))); ?>"><?php echo e(old($column->column_name)); ?></textarea>
                        <?php elseif($inputType === 'select'): ?>
                            <select name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                    class="<?php echo e($inputClass); ?>"
                                    <?php echo e(!$column->nullable && $column->default === null ? 'required' : ''); ?>>
                                <?php if($column->nullable || $column->default !== null): ?>
                                    <option value="">-- Select --</option>
                                <?php endif; ?>
                                <?php $__currentLoopData = $enumValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enumValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($enumValue); ?>" <?php echo e(old($column->column_name) == $enumValue ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($enumValue)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php elseif($inputType === 'checkbox'): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="<?php echo e($column->column_name); ?>" value="0">
                                    <input type="checkbox" name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                           value="1" <?php echo e(old($column->column_name) ? 'checked' : ''); ?>>
                                    Yes
                                </label>
                            </div>
                        <?php else: ?>
                            <input type="<?php echo e($inputType); ?>" name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                   class="<?php echo e($inputClass); ?>" value="<?php echo e(old($column->column_name)); ?>"
                                   <?php echo e(!$column->nullable && $column->default === null ? 'required' : ''); ?>

                                   <?php echo e($placeholder); ?>

                                   <?php if($inputType === 'number' && strpos($column->type, 'decimal') !== false): ?>
                                       step="0.01"
                                   <?php endif; ?>
                                   placeholder="Enter <?php echo e(strtolower(str_replace('_', ' ', $column->column_name))); ?>">
                        <?php endif; ?>

                        <?php if($errors->has($column->column_name)): ?>
                            <span class="help-block"><?php echo e($errors->first($column->column_name)); ?></span>
                        <?php endif; ?>

                        <small class="help-block">
                            Type: <code><?php echo e($column->type); ?></code>
                            <?php if(!$column->nullable): ?>
                                <span class="text-danger">(Required)</span>
                            <?php endif; ?>
                            <?php if($column->default !== null): ?>
                                <span class="text-muted">(Default: <?php echo e($column->default); ?>)</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Record
                            </button>
                            <a href="<?php echo e(route('admin.database.table', $tableName)); ?>" class="btn btn-default">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                <span class="text-danger">*</span> Required fields
                            </small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/admin/database/create.blade.php ENDPATH**/ ?>