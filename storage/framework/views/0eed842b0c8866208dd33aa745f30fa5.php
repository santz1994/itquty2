

<?php $__env->startSection('htmlheader_title'); ?>
    Asset Maintenance Logs
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Asset Maintenance Logs
            <small>Manage asset maintenance and repair history</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo e(url('/home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Maintenance Logs</li>
        </ol>
    </section>

    <section class="content">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Success!</h4>
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Maintenance Logs</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('maintenance.create')); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Add Maintenance Log
                    </a>
                </div>
            </div>

            <div class="box-body">
                <!-- Filters -->
                <div class="row">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group">
                                <label>Asset:</label>
                                <select name="asset_id" class="form-control">
                                    <option value="">All Assets</option>
                                    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($asset->id); ?>" <?php echo e(request('asset_id') == $asset->id ? 'selected' : ''); ?>>
                                            <?php echo e($asset->asset_tag); ?> - <?php echo e($asset->model_name ?? 'Unknown Model'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Status:</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($status); ?>" <?php echo e(request('status') == $status ? 'selected' : ''); ?>>
                                            <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Type:</label>
                                <select name="maintenance_type" class="form-control">
                                    <option value="">All Types</option>
                                    <?php $__currentLoopData = $maintenanceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type); ?>" <?php echo e(request('maintenance_type') == $type ? 'selected' : ''); ?>>
                                            <?php echo e(ucfirst($type)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-default">Filter</button>
                            <a href="<?php echo e(route('maintenance.index')); ?>" class="btn btn-default">Clear</a>
                        </form>
                    </div>
                </div>
                <br>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Scheduled</th>
                                <th>Performed By</th>
                                <th>Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $maintenanceLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($log->asset->asset_tag ?? 'N/A'); ?></strong><br>
                                        <small><?php echo e($log->asset->name ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <span class="label label-info"><?php echo e(ucfirst($log->maintenance_type)); ?></span>
                                    </td>
                                    <td><?php echo e(\Illuminate\Support\Str::limit($log->description, 50)); ?></td>
                                    <td>
                                        <?php
                                            $statusClass = [
                                                'planned' => 'label-default',
                                                'in_progress' => 'label-warning', 
                                                'completed' => 'label-success',
                                                'cancelled' => 'label-danger'
                                            ];
                                        ?>
                                        <span class="label <?php echo e($statusClass[$log->status] ?? 'label-default'); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $log->status))); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($log->scheduled_at ? $log->scheduled_at->format('M d, Y') : '-'); ?></td>
                                    <td><?php echo e($log->performedBy->name ?? 'N/A'); ?></td>
                                    <td><?php echo e($log->cost ? 'Rp ' . number_format($log->cost, 0, ',', '.') : '-'); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('maintenance.show', $log->id)); ?>" class="btn btn-xs btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('maintenance.edit', $log->id)); ?>" class="btn btn-xs btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="<?php echo e(route('maintenance.destroy', $log->id)); ?>" style="display: inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-xs btn-danger" 
                                                        onclick="return confirm('Are you sure?')">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No maintenance logs found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($maintenanceLogs->hasPages()): ?>
                    <div class="text-center">
                        <?php echo e($maintenanceLogs->appends(request()->query())->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\maintenance\index.blade.php ENDPATH**/ ?>