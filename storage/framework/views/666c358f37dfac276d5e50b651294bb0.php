

<?php $__env->startSection('page_title'); ?>
    Database Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Manage database tables and records with full CRUD operations
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <!-- Database Overview -->
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-database"></i> Database Overview
                </h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('admin.database.backup')); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-download"></i> Full Backup
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="fa fa-table"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Tables</span>
                                <span class="info-box-number"><?php echo e($stats['total_tables']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-hdd-o"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Database Size</span>
                                <span class="info-box-number"><?php echo e(number_format($stats['database_size'] / 1024 / 1024, 2)); ?> MB</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-plug"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Connection</span>
                                <span class="info-box-number"><?php echo e($stats['connection']); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-server"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Database</span>
                                <span class="info-box-number"><?php echo e($stats['database_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables List -->
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-list"></i> Database Tables
                </h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="tablesTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Table Name</th>
                                <th>Rows</th>
                                <th>Data Size</th>
                                <th>Index Size</th>
                                <th>Total Size</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $stats = $tableStats[$table] ?? null;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($table); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-blue">
                                        <?php echo e($stats ? number_format($stats->row_count ?? 0) : 'N/A'); ?>

                                    </span>
                                </td>
                                <td><?php echo e($stats ? number_format(($stats->data_size ?? 0) / 1024, 2) . ' KB' : 'N/A'); ?></td>
                                <td><?php echo e($stats ? number_format(($stats->index_size ?? 0) / 1024, 2) . ' KB' : 'N/A'); ?></td>
                                <td><?php echo e($stats ? number_format(($stats->total_size ?? 0) / 1024, 2) . ' KB' : 'N/A'); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('admin.database.table', $table)); ?>" class="btn btn-info" title="View Table">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.database.create', $table)); ?>" class="btn btn-success" title="Add Record">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" title="Export">
                                                <i class="fa fa-download"></i> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="<?php echo e(route('admin.database.export', [$table, 'csv'])); ?>">Export CSV</a></li>
                                                <li><a href="<?php echo e(route('admin.database.export', [$table, 'sql'])); ?>">Export SQL</a></li>
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-danger" onclick="confirmTruncate('<?php echo e($table); ?>')" title="Truncate Table">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Truncate Confirmation Modal -->
<div class="modal fade" id="truncateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Truncate</h4>
            </div>
            <div class="modal-body">
                <p><strong>WARNING:</strong> This will permanently delete all data from the table <span id="tableNameDisplay"></span>.</p>
                <p>This action cannot be undone. Are you sure you want to continue?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <form id="truncateForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Yes, Truncate Table</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
<script>
$(document).ready(function() {
    $('#tablesTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[ 0, "asc" ]]
    });
});

function confirmTruncate(tableName) {
    $('#tableNameDisplay').text(tableName);
    $('#truncateForm').attr('action', '/admin/database/' + tableName + '/truncate');
    $('#truncateModal').modal('show');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/admin/database/index.blade.php ENDPATH**/ ?>