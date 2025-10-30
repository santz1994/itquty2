

<?php $__env->startSection('title', 'Import Assets'); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-upload"></i> Import Assets</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('assets.download-template')); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> Download Template
                    </a>
                    <a href="<?php echo e(route('assets.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Assets
                    </a>
                </div>
            </div>
            
            <form action="<?php echo e(route('assets.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="box-body">
                    <?php if(session('import_summary')): ?>
                        <?php $summary = session('import_summary'); ?>
                        <div class="alert alert-success">
                            <strong><?php echo e($summary['created'] ?? 0); ?> assets imported.</strong>
                        </div>

                        <?php if(!empty($summary['errors'])): ?>
                            <div class="box box-danger">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Import Errors</h4>
                                    <div class="box-tools pull-right">
                                        <a href="<?php echo e(route('assets.import-errors-download')); ?>" class="btn btn-sm btn-warning">
                                            <i class="fa fa-download"></i> Download Errors CSV
                                        </a>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <table class="table table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>Row</th>
                                                <th>Messages</th>
                                                <th>Data</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $summary['errors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($err['row'] ?? '?'); ?></td>
                                                <td>
                                                    <?php if(!empty($err['errors'])): ?>
                                                        <ul class="mb-0">
                                                            <?php $__currentLoopData = $err['errors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <li><?php echo e($m); ?></li>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <?php echo e($err['error'] ?? 'Unknown error'); ?>

                                                    <?php endif; ?>
                                                </td>
                                                <td><pre style="white-space:pre-wrap"><?php echo e(json_encode($err['data'] ?? [], JSON_PRETTY_PRINT)); ?></pre></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="file">Choose Excel File</label>
                        <input type="file" 
                               class="form-control" 
                               id="file" 
                               name="file" 
                               accept=".xlsx,.xls,.csv"
                               required>
                        <small class="help-block">
                            Accepted formats: .xlsx, .xls, .csv (Max size: 2MB)
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <h4><i class="icon fa fa-info"></i> Import Instructions:</h4>
                        <ol>
                            <li>Download the template file using the button above</li>
                            <li>Fill in your asset data following the template format</li>
                            <li>Make sure all required fields are filled</li>
                            <li>Asset tags must be unique</li>
                            <li>Dates should be in YYYY-MM-DD format</li>
                            <li>Upload the completed file using the form above</li>
                        </ol>
                    </div>
                </div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-upload"></i> Import Assets
                    </button>
                    <a href="<?php echo e(route('assets.index')); ?>" class="btn btn-default">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\assets\import.blade.php ENDPATH**/ ?>