

<?php $__env->startSection('page_title'); ?>
    <?php echo e($pageTitle); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Edit record #<?php echo e($id); ?> in <?php echo e($tableName); ?> table
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-edit"></i> Edit Record #<?php echo e($id); ?>

                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('admin.database.table', $tableName)); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Table
                    </a>
                </div>
            </div>
            
            <form method="POST" action="<?php echo e(route('admin.database.update', [$tableName, $id])); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
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

                            <?php if(!$column->nullable && $column->default === null && $column->column_name !== 'updated_at'): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        
                        <?php
                            $currentValue = $record->{$column->column_name} ?? '';
                            $oldValue = old($column->column_name, $currentValue);
                            
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
                                if ($oldValue && $oldValue !== '0000-00-00') {
                                    $oldValue = \Carbon\Carbon::parse($oldValue)->format('Y-m-d');
                                } else {
                                    $oldValue = '';
                                }
                            } elseif (strpos($column->type, 'datetime') !== false || strpos($column->type, 'timestamp') !== false) {
                                $inputType = 'datetime-local';
                                if ($oldValue && $oldValue !== '0000-00-00 00:00:00') {
                                    $oldValue = \Carbon\Carbon::parse($oldValue)->format('Y-m-d\TH:i');
                                } else {
                                    $oldValue = '';
                                }
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
                            
                            // Special handling for readonly fields
                            $isReadonly = in_array($column->column_name, ['id']) || $column->auto_increment;
                        ?>

                        <?php if($isReadonly): ?>
                            <input type="text" class="form-control" value="<?php echo e($oldValue); ?>" readonly>
                            <input type="hidden" name="<?php echo e($column->column_name); ?>" value="<?php echo e($oldValue); ?>">
                        <?php elseif($inputType === 'textarea'): ?>
                            <textarea name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                      class="<?php echo e($inputClass); ?>" rows="4"
                                      <?php echo e(!$column->nullable && $column->default === null && $column->column_name !== 'updated_at' ? 'required' : ''); ?>

                                      placeholder="Enter <?php echo e(strtolower(str_replace('_', ' ', $column->column_name))); ?>"><?php echo e($oldValue); ?></textarea>
                        <?php elseif($inputType === 'select'): ?>
                            <select name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                    class="<?php echo e($inputClass); ?>"
                                    <?php echo e(!$column->nullable && $column->default === null && $column->column_name !== 'updated_at' ? 'required' : ''); ?>>
                                <?php if($column->nullable || $column->default !== null): ?>
                                    <option value="">-- Select --</option>
                                <?php endif; ?>
                                <?php $__currentLoopData = $enumValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enumValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($enumValue); ?>" <?php echo e($oldValue == $enumValue ? 'selected' : ''); ?>>
                                        <?php echo e(ucfirst($enumValue)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php elseif($inputType === 'checkbox'): ?>
                            <div class="checkbox">
                                <label>
                                    <input type="hidden" name="<?php echo e($column->column_name); ?>" value="0">
                                    <input type="checkbox" name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                           value="1" <?php echo e($oldValue ? 'checked' : ''); ?>>
                                    Yes
                                </label>
                            </div>
                        <?php else: ?>
                            <input type="<?php echo e($inputType); ?>" name="<?php echo e($column->column_name); ?>" id="<?php echo e($column->column_name); ?>" 
                                   class="<?php echo e($inputClass); ?>" value="<?php echo e($oldValue); ?>"
                                   <?php echo e(!$column->nullable && $column->default === null && $column->column_name !== 'updated_at' ? 'required' : ''); ?>

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
                            <?php if(!$column->nullable && $column->column_name !== 'updated_at'): ?>
                                <span class="text-danger">(Required)</span>
                            <?php endif; ?>
                            <?php if($column->default !== null): ?>
                                <span class="text-muted">(Default: <?php echo e($column->default); ?>)</span>
                            <?php endif; ?>
                            <?php if($isReadonly): ?>
                                <span class="text-info">(Read-only)</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-save"></i> Update Record
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\database\edit.blade.php ENDPATH**/ ?>