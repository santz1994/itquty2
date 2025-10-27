

<?php $__env->startSection('main-content'); ?>


<?php echo $__env->make('components.page-header', [
    'title' => 'Asset Requests',
    'subtitle' => 'Manage asset requests and approvals',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Asset Requests']
    ],
    'actions' => '<a href="'.route('asset-requests.create').'" class="btn btn-primary">
        <i class="fa fa-plus"></i> Create New Request
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            
            
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-filter"></i> Filters
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('asset-requests.index')); ?>" id="filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">All Status</option>
                                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($status); ?>" <?php echo e(request('status') == $status ? 'selected' : ''); ?>>
                                                <?php echo e(ucfirst($status)); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="asset_type">Asset Type</label>
                                    <select name="asset_type" id="asset_type" class="form-control">
                                        <option value="">All Types</option>
                                        <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($type->id); ?>" <?php echo e(request('asset_type') == $type->id ? 'selected' : ''); ?>>
                                                <?php echo e($type->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select name="priority" id="priority" class="form-control">
                                        <option value="">All Priorities</option>
                                        <?php $__currentLoopData = $priorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($priority); ?>" <?php echo e(request('priority') == $priority ? 'selected' : ''); ?>>
                                                <?php echo e(ucfirst($priority)); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Filter
                                        </button>
                                        <a href="<?php echo e(route('asset-requests.index')); ?>" class="btn btn-secondary">
                                            <i class="fa fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-list"></i> Asset Requests
                    <span class="badge badge-primary float-right"><?php echo e($requests->total()); ?> Total</span>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-enhanced table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="sortable" data-column="id">ID</th>
                                    <th class="sortable" data-column="title">Title</th>
                                    <th class="sortable" data-column="asset_type">Asset Type</th>
                                    <th class="sortable" data-column="requested_by">Requested By</th>
                                    <th class="sortable" data-column="priority">Priority</th>
                                    <th class="sortable" data-column="status">Status</th>
                                    <th class="sortable" data-column="needed_date">Needed Date</th>
                                    <th class="sortable" data-column="created_at">Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>#<?php echo e($request->id); ?></td>
                                        <td>
                                            <strong><?php echo e($request->title); ?></strong>
                                            <?php if($request->requested_quantity > 1): ?>
                                                <br><small class="text-muted">Qty: <?php echo e($request->requested_quantity); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($request->assetType): ?>
                                                <span class="badge badge-info"><?php echo e($request->assetType->name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($request->requestedBy): ?>
                                                <?php echo e($request->requestedBy->name); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                                $priorityColors = [
                                                    'low' => 'secondary',
                                                    'medium' => 'info',
                                                    'high' => 'warning',
                                                    'urgent' => 'danger'
                                                ];
                                                $color = $priorityColors[$request->priority] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?php echo e($color); ?>"><?php echo e(ucfirst($request->priority)); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'fulfilled' => 'primary'
                                                ];
                                                $statusColor = $statusColors[$request->status] ?? 'secondary';
                                            ?>
                                            <span class="badge badge-<?php echo e($statusColor); ?>"><?php echo e(ucfirst($request->status)); ?></span>
                                        </td>
                                        <td><?php echo e(\Carbon\Carbon::parse($request->needed_date)->format('d M Y')); ?></td>
                                        <td><?php echo e($request->created_at->format('d M Y')); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo e(route('asset-requests.show', $request->id)); ?>" 
                                                   class="btn btn-sm btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if($request->status === 'pending'): ?>
                                                    <a href="<?php echo e(route('asset-requests.edit', $request->id)); ?>" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(Auth::user()->hasRole(['admin', 'super-admin']) && $request->status === 'pending'): ?>
                                                    <form action="<?php echo e(route('asset-requests.approve', $request->id)); ?>" 
                                                          method="POST" style="display: inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                title="Approve"
                                                                onclick="return confirm('Approve this request?')">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="<?php echo e(route('asset-requests.reject', $request->id)); ?>" 
                                                          method="POST" style="display: inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                title="Reject"
                                                                onclick="return confirm('Reject this request?')">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                            No asset requests found.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="mt-3">
                        <?php echo e($requests->appends(request()->query())->links()); ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Auto-submit filters
    $('#status, #asset_type, #priority').on('change', function() {
        showLoading('Filtering requests...');
        $('#filter-form').submit();
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/asset-requests/index.blade.php ENDPATH**/ ?>