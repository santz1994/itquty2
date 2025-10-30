

<?php $__env->startSection('title'); ?>
Asset Maintenance Analytics
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Asset Maintenance Analytics</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('asset-maintenance.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Maintenance
                    </a>
                </div>
            </div>
            <div class="box-body">
                <!-- Date Range Filter -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <form method="GET" class="form-inline">
                            <div class="form-group">
                                <label for="date_from">From:</label>
                                <input type="date" id="date_from" name="date_from" class="form-control" value="<?php echo e($dateFrom); ?>">
                            </div>
                            <div class="form-group" style="margin-left: 10px;">
                                <label for="date_to">To:</label>
                                <input type="date" id="date_to" name="date_to" class="form-control" value="<?php echo e($dateTo); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary" style="margin-left: 10px;">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3><?php echo e($analytics['total_maintenance_tickets']); ?></h3>
                                <p>Total Maintenance Tickets</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-wrench"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3><?php echo e($analytics['completed_maintenance']); ?></h3>
                                <p>Completed Maintenance</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3><?php echo e($analytics['pending_maintenance']); ?></h3>
                                <p>Pending Maintenance</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3><?php echo e(number_format($analytics['average_resolution_time'], 1)); ?>h</h3>
                                <p>Avg Resolution Time</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <!-- Maintenance by Month Chart -->
                    <div class="col-md-8">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Maintenance Tickets by Month</h3>
                            </div>
                            <div class="box-body">
                                <div style="height: 300px; display: flex; align-items: center; justify-content: center; background: #f9f9f9; border: 1px dashed #ddd;">
                                    <div class="text-center">
                                        <i class="fa fa-bar-chart fa-3x text-muted"></i>
                                        <p class="text-muted">Chart will be displayed here</p>
                                        <small class="text-muted">Data: <?php echo e(json_encode($analytics['maintenance_by_month'])); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Estimates -->
                    <div class="col-md-4">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Cost Estimates</h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Total Estimated Cost:</strong></td>
                                        <td>Rp <?php echo e(number_format($analytics['cost_estimates']['total_estimated_cost'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Completed Cost:</strong></td>
                                        <td>Rp <?php echo e(number_format($analytics['cost_estimates']['completed_cost'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pending Cost:</strong></td>
                                        <td>Rp <?php echo e(number_format($analytics['cost_estimates']['pending_cost'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Avg Cost per Asset:</strong></td>
                                        <td>Rp <?php echo e(number_format($analytics['cost_estimates']['average_cost_per_asset'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Most Problematic Assets -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-danger">
                            <div class="box-header with-border">
                                <h3 class="box-title">Most Problematic Assets</h3>
                            </div>
                            <div class="box-body">
                                <?php if(!empty($analytics['most_problematic_assets']) && count($analytics['most_problematic_assets']) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Asset Tag</th>
                                                <th>Asset Name</th>
                                                <th>Location</th>
                                                <th>Maintenance Count</th>
                                                <th>Last Maintenance</th>
                                                <th>Priority</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $analytics['most_problematic_assets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><strong><?php echo e($asset['asset_tag'] ?? 'N/A'); ?></strong></td>
                                                <td><?php echo e($asset['name'] ?? 'N/A'); ?></td>
                                                <td><?php echo e($asset['location'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge bg-red"><?php echo e($asset['maintenance_count'] ?? 0); ?></span>
                                                </td>
                                                <td><?php echo e($asset['last_maintenance'] ?? 'Never'); ?></td>
                                                <td>
                                                    <?php if(($asset['maintenance_count'] ?? 0) >= 5): ?>
                                                        <span class="label label-danger">Critical</span>
                                                    <?php elseif(($asset['maintenance_count'] ?? 0) >= 3): ?>
                                                        <span class="label label-warning">High</span>
                                                    <?php else: ?>
                                                        <span class="label label-info">Medium</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="text-center" style="padding: 40px;">
                                    <i class="fa fa-check-circle fa-3x text-success"></i>
                                    <h4>No Problematic Assets Found</h4>
                                    <p class="text-muted">All assets are performing well within the selected date range.</p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
<script>
$(document).ready(function() {
    // You can add Chart.js or other charting library here
    console.log('Analytics data:', <?php echo json_encode($analytics); ?>);
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\asset-maintenance\analytics.blade.php ENDPATH**/ ?>