

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

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-circle"></i> <strong>Validation errors:</strong>
            <ul style="margin-bottom: 0; margin-top: 5px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua" onclick="filterByStatus('all')" style="cursor: pointer;">
                <div class="inner">
                    <h3><?php echo e($stats['total'] ?? 0); ?></h3>
                    <p>Total Requests</p>
                </div>
                <div class="icon">
                    <i class="fa fa-inbox"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow" onclick="filterByStatus('pending')" style="cursor: pointer;">
                <div class="inner">
                    <h3><?php echo e($stats['pending'] ?? 0); ?></h3>
                    <p>Pending Approval</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green" onclick="filterByStatus('approved')" style="cursor: pointer;">
                <div class="inner">
                    <h3><?php echo e($stats['approved'] ?? 0); ?></h3>
                    <p>Approved</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red" onclick="filterByStatus('rejected')" style="cursor: pointer;">
                <div class="inner">
                    <h3><?php echo e($stats['rejected'] ?? 0); ?></h3>
                    <p>Rejected</p>
                </div>
                <div class="icon">
                    <i class="fa fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            
            
            <div class="box box-default collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Advanced Filters</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body filter-bar">
                    <form method="GET" action="<?php echo e(route('asset-requests.index')); ?>" id="filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status"><i class="fa fa-info-circle"></i> Status</label>
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
                                    <label for="asset_type"><i class="fa fa-box"></i> Asset Type</label>
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
                                    <label for="priority"><i class="fa fa-exclamation-triangle"></i> Priority</label>
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
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-search"></i> Apply Filters
                                        </button>
                                        <a href="<?php echo e(route('asset-requests.index')); ?>" class="btn btn-default btn-block" style="margin-top: 5px;">
                                            <i class="fa fa-times"></i> Clear All
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-clipboard-list"></i> Asset Requests</h3>
                    <span class="count-badge"><?php echo e(method_exists($requests, 'total') ? $requests->total() : count($requests)); ?></span>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="requests-table" class="table table-enhanced table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Request #</th>
                                    <th>Title</th>
                                    <th style="width: 120px;">Asset Type</th>
                                    <th>Requested By</th>
                                    <th style="width: 100px;">Priority</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 110px;">Needed Date</th>
                                    <th style="width: 100px;">Created</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>#<?php echo e($request->id); ?></td>
                                        <td><strong><?php echo e($request->request_number ?? '-'); ?></strong></td>
                                        <td>
                                            <strong><?php echo e($request->title); ?></strong>
                                            <?php if($request->requested_quantity > 1): ?>
                                                <br><small class="text-muted"><i class="fa fa-box"></i> Qty: <?php echo e($request->requested_quantity); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($request->assetType): ?>
                                                <span class="label label-info"><?php echo e($request->assetType->name); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($request->requestedBy): ?>
                                                <i class="fa fa-user"></i> <?php echo e($request->requestedBy->name); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($request->priority === 'urgent'): ?>
                                                <span class="label label-danger"><i class="fa fa-bolt"></i> Urgent</span>
                                            <?php elseif($request->priority === 'high'): ?>
                                                <span class="label label-warning"><i class="fa fa-arrow-up"></i> High</span>
                                            <?php elseif($request->priority === 'medium'): ?>
                                                <span class="label label-info"><i class="fa fa-minus"></i> Medium</span>
                                            <?php else: ?>
                                                <span class="label label-default"><i class="fa fa-arrow-down"></i> Low</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($request->status === 'fulfilled'): ?>
                                                <span class="label label-primary"><i class="fa fa-check-double"></i> Fulfilled</span>
                                            <?php elseif($request->status === 'approved'): ?>
                                                <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                                            <?php elseif($request->status === 'rejected'): ?>
                                                <span class="label label-danger"><i class="fa fa-times"></i> Rejected</span>
                                            <?php else: ?>
                                                <span class="label label-warning"><i class="fa fa-clock"></i> Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($request->needed_date): ?>
                                                <?php echo e(\Carbon\Carbon::parse($request->needed_date)->format('d M Y')); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($request->created_at->format('d M Y')); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo e(route('asset-requests.show', $request->id)); ?>" 
                                                   class="btn btn-xs btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if($request->status === 'pending'): ?>
                                                    <a href="<?php echo e(route('asset-requests.edit', $request->id)); ?>" 
                                                       class="btn btn-xs btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if(Auth::user()->hasRole(['admin', 'super-admin']) && $request->status === 'pending'): ?>
                                                    <form action="<?php echo e(route('asset-requests.approve', $request->id)); ?>" 
                                                          method="POST" style="display: inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-xs btn-success" 
                                                                title="Approve"
                                                                onclick="return confirm('Approve this request?')">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="<?php echo e(route('asset-requests.reject', $request->id)); ?>" 
                                                          method="POST" style="display: inline;">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-xs btn-danger" 
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
                                        <td colspan="10" class="text-center empty-state">
                                            <i class="fa fa-inbox fa-3x" style="opacity: 0.3; margin-bottom: 15px;"></i>
                                            <p>No asset requests found.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
    // Move export buttons to header
    table.buttons().container()
        .appendTo($('.box-header .box-title').parent());

    // Clickable stat cards filtering
    window.filterByStatus = function(status) {
        if (status === 'all') {
            table.search('').draw();
        } else {
            table.search(status).draw();
        }
    };

    // Auto-submit filters
    $('#status, #asset_type, #priority').on('change', function() {
        $('#filter-form').submit();
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/asset-requests/index.blade.php ENDPATH**/ ?>