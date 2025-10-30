
<?php
    $tableId = $tableId ?? 'data-table';
    $tableClass = $tableClass ?? 'table table-bordered table-striped';
    $showSearch = $showSearch ?? true;
    $showExport = $showExport ?? true;
?>

<div class="table-responsive">
    <table id="<?php echo e($tableId); ?>" class="<?php echo e($tableClass); ?>">
        <thead>
            <tr>
                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th><?php echo e($column['title']); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if(isset($actions) && $actions): ?>
                    <th width="120">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php echo e($slot); ?>

        </tbody>
    </table>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(function () {
    $('#<?php echo e($tableId); ?>').DataTable({
        'paging': true,
        'lengthChange': true,
        'searching': <?php echo e($showSearch ? 'true' : 'false'); ?>,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'responsive': true,
        <?php if($showExport): ?>
        'buttons': [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-primary'
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-primary'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-primary'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-primary'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-primary'
            }
        ],
        'dom': 'Bfrtip'
        <?php endif; ?>
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('plugins/datatables/dataTables.bootstrap.css')); ?>">
<?php if($showExport): ?>
<link rel="stylesheet" href="<?php echo e(asset('plugins/datatables/extensions/Buttons/css/buttons.bootstrap.min.css')); ?>">
<?php endif; ?>
<?php $__env->stopPush(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\partials\data-table.blade.php ENDPATH**/ ?>