

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', ['title' => 'Master Import/Export', 'subtitle' => 'Consolidated master data import and export'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Exports</h3></div>
            <div class="box-body">
                <p>Feature-specific exports:</p>
                <ul>
                    <li><a href="<?php echo e(route('assets.export')); ?>">Export Assets</a></li>
                    <li><a href="<?php echo e(route('tickets.export')); ?>">Export Tickets</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Imports</h3></div>
            <div class="box-body">
                <p>Master imports let you upload CSVs for lookup/master data (locations, manufacturers, types).</p>
                <a href="<?php echo e(route('masterdata.imports')); ?>" class="btn btn-primary">Go to Imports</a>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\masterdata\index.blade.php ENDPATH**/ ?>