

<?php $__env->startSection('title'); ?>
Enhanced Inventory Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
  <!-- Summary Statistics -->
  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-aqua">
      <div class="inner">
        <h3><?php echo e($stats['total_assets']); ?></h3>
        <p>Total Assets</p>
      </div>
      <div class="icon">
        <i class="fa fa-desktop"></i>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-green">
      <div class="inner">
        <h3><?php echo e($stats['active_assets']); ?></h3>
        <p>Active Assets</p>
      </div>
      <div class="icon">
        <i class="fa fa-check-circle"></i>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-yellow">
      <div class="inner">
        <h3><?php echo e($stats['maintenance_assets']); ?></h3>
        <p>In Maintenance</p>
      </div>
      <div class="icon">
        <i class="fa fa-wrench"></i>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-xs-6">
    <div class="small-box bg-red">
      <div class="inner">
        <h3><?php echo e($stats['pending_requests']); ?></h3>
        <p>Pending Requests</p>
      </div>
      <div class="icon">
        <i class="fa fa-clock-o"></i>
      </div>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="row">
  <div class="col-md-12">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">
          <i class="fa fa-list"></i> Inventory Management
        </h3>
        <div class="box-tools pull-right">
          <a href="<?php echo e(route('assets.create')); ?>" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Add New Asset
          </a>
          <a href="<?php echo e(route('asset-requests.create')); ?>" class="btn btn-success btn-sm">
            <i class="fa fa-paper-plane"></i> Request Asset
          </a>
        </div>
      </div>

      <!-- Filters -->
      <div class="box-body">
        <form method="GET" action="<?php echo e(route('assets.index')); ?>">
          <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control">
                  <option value="">All Categories</option>
                  <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                      <?php echo e($category->type_name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                  <option value="">All Statuses</option>
                  <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($status->id); ?>" <?php echo e(request('status') == $status->id ? 'selected' : ''); ?>>
                      <?php echo e($status->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Location</label>
                <select name="location" class="form-control">
                  <option value="">All Locations</option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>" <?php echo e(request('location') == $location->id ? 'selected' : ''); ?>>
                      <?php echo e($location->location_name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Division</label>
                <select name="division" class="form-control">
                  <option value="">All Divisions</option>
                  <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($division->id); ?>" <?php echo e(request('division') == $division->id ? 'selected' : ''); ?>>
                      <?php echo e($division->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>Search</label>
                <input type="text" name="search" class="form-control" placeholder="Asset Tag, Serial..." value="<?php echo e(request('search')); ?>">
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <div>
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-search"></i> Filter
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Assets List -->
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Assets Inventory</h3>
        <div class="box-tools pull-right">
          <span class="label label-info"><?php echo e($assets->total()); ?> Total Assets</span>
        </div>
      </div>
      
      <div class="box-body">
        <?php if($assets->count() > 0): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="assetsTable">
            <thead>
              <tr>
                <th>Asset Tag</th>
                <th>Category</th>
                <th>Model</th>
                <th>Serial Number</th>
                <th>Status</th>
                <th>Location</th>
                <th>Division</th>
                <th>Assigned To</th>
                <th>Purchase Date</th>
                <th>Warranty</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr class="<?php echo e($asset->status->name == 'In Repair' ? 'warning' : ($asset->status->name == 'Retired' ? 'danger' : '')); ?>">
                <td>
                  <strong><?php echo e($asset->asset_tag); ?></strong>
                  <?php if($asset->qr_code): ?>
                    <br><small class="text-muted"><i class="fa fa-qrcode"></i> <?php echo e($asset->qr_code); ?></small>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge bg-blue"><?php echo e($asset->assetModel->assetType->type_name ?? 'N/A'); ?></span>
                </td>
                <td><?php echo e($asset->assetModel->name ?? 'N/A'); ?></td>
                <td><?php echo e($asset->serial_number); ?></td>
                <td>
                  <?php
                    $statusColor = 'default';
                    switch($asset->status->name ?? 'Unknown') {
                      case 'Active': $statusColor = 'success'; break;
                      case 'In Use': $statusColor = 'primary'; break;
                      case 'Available': $statusColor = 'info'; break;
                      case 'In Repair': $statusColor = 'warning'; break;
                      case 'Retired': $statusColor = 'danger'; break;
                    }
                  ?>
                  <span class="label label-<?php echo e($statusColor); ?>"><?php echo e($asset->status->name ?? 'Unknown'); ?></span>
                </td>
                <td><?php echo e($asset->movement->location->location_name ?? 'N/A'); ?></td>
                <td><?php echo e($asset->division->name ?? 'N/A'); ?></td>
                <td><?php echo e($asset->assignedUser->name ?? 'Unassigned'); ?></td>
                <td><?php echo e($asset->purchase_date ? $asset->purchase_date->format('M d, Y') : 'N/A'); ?></td>
                <td>
                  <?php if($asset->purchase_date && $asset->warranty_months): ?>
                    <?php
                      $warrantyStatus = $asset->getWarrantyStatus();
                    ?>
                    <span class="label label-<?php echo e($warrantyStatus == 'Active' ? 'success' : ($warrantyStatus == 'Expiring soon' ? 'warning' : 'danger')); ?>">
                      <?php echo e($warrantyStatus); ?>

                    </span>
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="btn-group">
                    <a href="<?php echo e(route('assets.show', $asset->id)); ?>" class="btn btn-xs btn-info" title="View Details">
                      <i class="fa fa-eye"></i>
                    </a>
                    <a href="<?php echo e(route('assets.edit', $asset->id)); ?>" class="btn btn-xs btn-primary" title="Edit">
                      <i class="fa fa-edit"></i>
                    </a>
                    <a href="<?php echo e(route('assets.ticket-history', $asset->id)); ?>" class="btn btn-xs btn-warning" title="Ticket History">
                      <i class="fa fa-history"></i>
                    </a>
                    <div class="btn-group">
                      <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i> <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a href="#" onclick="changeAssetStatus(<?php echo e($asset->id); ?>, 'active')"><i class="fa fa-check"></i> Mark Active</a></li>
                        <li><a href="#" onclick="changeAssetStatus(<?php echo e($asset->id); ?>, 'maintenance')"><i class="fa fa-wrench"></i> Send to Maintenance</a></li>
                        <li><a href="#" onclick="changeAssetStatus(<?php echo e($asset->id); ?>, 'retired')"><i class="fa fa-times"></i> Retire Asset</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo e(route('tickets.create-with-asset', ['asset_id' => $asset->id])); ?>"><i class="fa fa-ticket"></i> Create Ticket</a></li>
                      </ul>
                    </div>
                  </div>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="text-center">
          <?php echo e($assets->appends(request()->query())->links()); ?>

        </div>

        <?php else: ?>
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> No assets found with the selected filters.
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- Asset Categories Overview -->
<div class="row">
  <div class="col-md-12">
    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Asset Categories Overview</h3>
      </div>
      
      <div class="box-body">
        <div class="row">
          <?php $__currentLoopData = $categoryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="col-md-3">
            <div class="info-box">
              <span class="info-box-icon bg-aqua">
                <i class="fa fa-<?php echo e($category['icon']); ?>"></i>
              </span>
              <div class="info-box-content">
                <span class="info-box-text"><?php echo e($category['name']); ?></span>
                <span class="info-box-number"><?php echo e($category['count']); ?></span>
                <div class="progress">
                  <div class="progress-bar" style="width: <?php echo e($category['percentage']); ?>%"></div>
                </div>
                <span class="progress-description"><?php echo e($category['percentage']); ?>% of total assets</span>
              </div>
            </div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('#assetsTable').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false, // We have custom search
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 25
        });
    }
});

function changeAssetStatus(assetId, status) {
    swal({
        title: 'Change Asset Status?',
        text: 'Are you sure you want to change this asset status to ' + status + '?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Yes, Change Status',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            // Create a form and submit
            var form = $('<form method="POST" action="/assets/' + assetId + '/change-status">')
                .append('<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">')
                .append('<input type="hidden" name="status" value="' + status + '">');
            
            $('body').append(form);
            form.submit();
        }
    });
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/inventory/index.blade.php ENDPATH**/ ?>