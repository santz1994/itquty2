

<?php $__env->startSection('main-content'); ?>

<style>
/* Enhanced filter bar styling */
.filter-bar {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 1px solid #e3e6f0;
}
.filter-bar .form-group {
    margin-bottom: 10px;
}
.filter-bar label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
}
/* Enhanced status badges */
.status-badge {
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
/* Quick action buttons in header */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
}
/* Clickable badges for filtering */
.clickable-badge {
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}
.clickable-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
/* Age indicators */
.age-danger { color: #d9534f; font-weight: bold; }
.age-warning { color: #f0ad4e; font-weight: bold; }
.age-success { color: #5cb85c; }
/* Enhanced table styling */
.table-enhanced thead th {
    background-color: #3c8dbc;
    color: white;
    font-weight: 600;
    border: 1px solid #2d6ca2;
}
.table-enhanced tbody tr:hover {
    background-color: #f0f8ff !important;
    cursor: pointer;
}
/* Info box hover effect */
.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}
</style>

<?php echo $__env->make('components.page-header', [
    'title' => 'Assets',
    'subtitle' => 'Manage all IT assets and equipment',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Assets']
    ],
    'actions' => '<div class="action-buttons">
        <a href="'.route('assets.create').'" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create New Asset
        </a>
        <a href="'.route('assets.import-form').'" class="btn btn-info">
            <i class="fa fa-upload"></i> Import
        </a>
        <a href="'.route('assets.export').'" class="btn btn-success">
            <i class="fa fa-download"></i> Export
        </a>
    </div>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  
  <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-check"></i> <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-ban"></i> <?php echo e(session('error')); ?>

    </div>
  <?php endif; ?>

  
  <div class="row">
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-purple clickable-badge" onclick="filterByStatus('all')">
        <div class="inner">
          <h3><?php echo e($totalAssets); ?></h3>
          <p>Total Assets</p>
        </div>
        <div class="icon">
          <i class="fa fa-tags"></i>
        </div>
        <a href="#" class="small-box-footer" onclick="event.preventDefault(); filterByStatus('all')">
          View All <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-aqua clickable-badge" onclick="filterByStatus('deployed')">
        <div class="inner">
          <h3><?php echo e($deployed); ?></h3>
          <p>Deployed</p>
        </div>
        <div class="icon">
          <i class="fa fa-check-circle"></i>
        </div>
        <a href="#" class="small-box-footer" onclick="event.preventDefault(); filterByStatus('deployed')">
          Filter <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-green clickable-badge" onclick="filterByStatus('ready')">
        <div class="inner">
          <h3><?php echo e($readyToDeploy); ?></h3>
          <p>Ready to Deploy</p>
        </div>
        <div class="icon">
          <i class="fa fa-plus-circle"></i>
        </div>
        <a href="#" class="small-box-footer" onclick="event.preventDefault(); filterByStatus('ready')">
          Filter <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-yellow clickable-badge" onclick="filterByStatus('repairs')">
        <div class="inner">
          <h3><?php echo e($repairs); ?></h3>
          <p>In Repairs</p>
        </div>
        <div class="icon">
          <i class="fa fa-wrench"></i>
        </div>
        <a href="#" class="small-box-footer" onclick="event.preventDefault(); filterByStatus('repairs')">
          Filter <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-red clickable-badge" onclick="filterByStatus('written-off')">
        <div class="inner">
          <h3><?php echo e($writtenOff); ?></h3>
          <p>Written Off</p>
        </div>
        <div class="icon">
          <i class="fa fa-times-circle"></i>
        </div>
        <a href="#" class="small-box-footer" onclick="event.preventDefault(); filterByStatus('written-off')">
          Filter <i class="fa fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Assets by Status</h3>
        </div>
        <div class="box-body">
          
          <form method="get" class="form-inline mb-2">
            <div class="form-group">
              <label for="per_page" class="mr-2">Show</label>
              <select name="per_page" id="per_page" class="form-control input-sm">
                <option value="10" <?php echo e(request('per_page') == '10' ? 'selected' : ''); ?>>10</option>
                <option value="25" <?php echo e(request('per_page') == '25' ? 'selected' : ''); ?>>25</option>
                <option value="50" <?php echo e(request('per_page') == '50' ? 'selected' : ''); ?>>50</option>
                <option value="100" <?php echo e(request('per_page') == '100' ? 'selected' : ''); ?>>100</option>
                <option value="all" <?php echo e(request('per_page') == 'all' || request()->boolean('all') ? 'selected' : ''); ?>>All</option>
              </select>
            </div>
            <div class="form-group ml-2">
              <button class="btn btn-default btn-sm">Apply</button>
            </div>
            
            <?php $__currentLoopData = request()->except(['per_page', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <input type="hidden" name="<?php echo e($k); ?>" value="<?php echo e($v); ?>" />
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </form>

          <?php if(!empty($assetsByStatus) && count($assetsByStatus) > 0): ?>
            <table class="table table-condensed">
              <thead>
                <tr><th>Status</th><th class="text-right">Count</th></tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $assetsByStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($s->status_name); ?></td>
                    <td class="text-right"><?php echo e($s->count); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No data available</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title">New Assets (Last 6 months)</h3>
        </div>
        <div class="box-body">
          <?php if(!empty($monthlyNewAssets) && count($monthlyNewAssets) > 0): ?>
            <div class="table-responsive">
              <table class="table table-condensed">
                <thead>
                  <tr><th>Month</th><th class="text-right">New Assets</th></tr>
                </thead>
                <tbody>
                  <?php $__currentLoopData = $monthlyNewAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                      <td><?php echo e($m['month'] ?? $m->month ?? 'N/A'); ?></td>
                      <td class="text-right"><?php echo e($m['count'] ?? $m->count ?? 0); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p>No recent assets</p>
          <?php endif; ?>
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
              <i class="fa fa-plus"></i> Expand Filters
            </button>
          </div>
        </div>
        <div class="box-body filter-bar">
          <form id="filterForm" method="get" action="<?php echo e(route('assets.index')); ?>">
            <div class="row">
              <div class="col-md-3 col-sm-6">
                <div class="form-group">
                  <label for="search"><i class="fa fa-search"></i> Search Assets</label>
                  <input type="text" id="search" name="search" class="form-control" 
                         placeholder="Tag, Serial, Model..." 
                         value="<?php echo e(request('search')); ?>">
                  <small class="text-muted">Search by asset tag, serial number, or model</small>
                </div>
              </div>
              <div class="col-md-2 col-sm-6">
                <div class="form-group">
                  <label for="status_filter"><i class="fa fa-info-circle"></i> Status</label>
                  <select id="status_filter" name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="deployed" <?php echo e(request('status') == 'deployed' ? 'selected' : ''); ?>>Deployed</option>
                    <option value="ready" <?php echo e(request('status') == 'ready' ? 'selected' : ''); ?>>Ready to Deploy</option>
                    <option value="repairs" <?php echo e(request('status') == 'repairs' ? 'selected' : ''); ?>>In Repairs</option>
                    <option value="written-off" <?php echo e(request('status') == 'written-off' ? 'selected' : ''); ?>>Written Off</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2 col-sm-6">
                <div class="form-group">
                  <label for="asset_type_filter"><i class="fa fa-laptop"></i> Asset Type</label>
                  <select id="asset_type_filter" name="asset_type" class="form-control">
                    <option value="">All Types</option>
                    <?php if(isset($assetTypes)): ?>
                      <?php $__currentLoopData = $assetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->id); ?>" <?php echo e(request('asset_type') == $type->id ? 'selected' : ''); ?>>
                          <?php echo e($type->type_name); ?>

                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2 col-sm-6">
                <div class="form-group">
                  <label for="location_filter"><i class="fa fa-map-marker"></i> Location</label>
                  <select id="location_filter" name="location" class="form-control">
                    <option value="">All Locations</option>
                    <?php if(isset($locations)): ?>
                      <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($location->id); ?>" <?php echo e(request('location') == $location->id ? 'selected' : ''); ?>>
                          <?php echo e($location->location_name); ?>

                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3 col-sm-6">
                <div class="form-group">
                  <label for="age_filter"><i class="fa fa-calendar"></i> Asset Age</label>
                  <select id="age_filter" name="age" class="form-control">
                    <option value="">All Ages</option>
                    <option value="new" <?php echo e(request('age') == 'new' ? 'selected' : ''); ?>>New (0-12 months)</option>
                    <option value="moderate" <?php echo e(request('age') == 'moderate' ? 'selected' : ''); ?>>Moderate (1-3 years)</option>
                    <option value="aging" <?php echo e(request('age') == 'aging' ? 'selected' : ''); ?>>Aging (3-5 years)</option>
                    <option value="old" <?php echo e(request('age') == 'old' ? 'selected' : ''); ?>>Old (5+ years)</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                  <i class="fa fa-filter"></i> Apply Filters
                </button>
                <a href="<?php echo e(route('assets.index')); ?>" class="btn btn-default">
                  <i class="fa fa-refresh"></i> Reset Filters
                </a>
                <button type="button" id="exportFiltered" class="btn btn-success pull-right">
                  <i class="fa fa-file-excel-o"></i> Export Filtered Results
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 col-xs-12 col-lg-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-table"></i> Assets List</h3>
          <div class="box-tools">
            <span class="label label-primary" id="assetCount"><?php echo e($assets->total() ?? count($assets)); ?> Assets</span>
          </div>
        </div>
        <div class="box-body">
          <table id="table" class="table table-enhanced table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="sortable" data-column="asset_tag">Tag</th>
                <th class="sortable" data-column="asset_type">Asset Type</th>
                <th class="sortable" data-column="serial_number">S/N</th>
                <th class="sortable" data-column="age">Age</th>
                <th class="sortable" data-column="model">Model</th>
                <th class="sortable" data-column="location">Location</th>
                <th class="sortable" data-column="division">Division</th>
                <th class="sortable" data-column="status">Status</th>
                <th class="actions">Actions</th>
                <th class="sortable" data-column="supplier">Supplier</th>
                <th class="sortable" data-column="purchase_date">Purchase Date</th>
                <th class="sortable" data-column="warranty_months">Warranty Months</th>
                <th class="sortable" data-column="warranty_type">Warranty Type</th>
              </tr>
            </thead>
            <tbody>
              <?php $now = new \Carbon\Carbon(); ?>
              <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($asset->purchase_date != '0000-00-00'): ?>
                  <?php $purchasedDate = \Carbon\Carbon::parse($asset->purchase_date);
                  $age = $purchasedDate->diffInMonths($now); ?>
                <?php endif; ?>
                <tr
                  <?php if(isset($age)): ?>
                    <?php if($age > 59): ?>
                      class="danger"
                    <?php elseif($age > 47 && $age < 60): ?>
                      class="warning"
                    <?php endif; ?>
                  <?php endif; ?>
                >
                  <div>
                    <td><?php echo e($asset->asset_tag); ?></td>
                    <td><?php echo e($asset->model && $asset->model->asset_type ? $asset->model->asset_type->type_name : 'N/A'); ?></td>
                    <td><?php echo e($asset->serial_number or ''); ?></td>
                    <td>
                      <?php if(isset($age)): ?>
                        <?php $years = $age / 12;
                              $months = $age % 12;
                               ?>
                        <?php echo e(floor($years)); ?>Y, <?php echo e($months); ?>M
                      <?php endif; ?>
                    </td>
                    <td>
                      <div id="model<?php echo e($asset->id); ?>" class="hover-pointer">
                        <?php echo e($asset->model ? (($asset->model->manufacturer ? $asset->model->manufacturer->name : 'N/A') . ' - ' . $asset->model->asset_model) : 'N/A'); ?>

                      </div>
                    </td>
                    <td>
                      <div id="location<?php echo e($asset->id); ?>" class="hover-pointer">
                        <?php echo e($asset->movement ? ($asset->movement->location ? $asset->movement->location->location_name : 'N/A') : 'N/A'); ?>

                      </div>
                    </td>
                    <td>
                      <div id="division<?php echo e($asset->id); ?>" class="hover-pointer">
                        <?php echo e($asset->division ? $asset->division->name : 'N/A'); ?>

                      </div>
                    </td>
                    <td>
                      <div id="status<?php echo e($asset->id); ?>" class="hover-pointer">
                        <?php if($asset->status): ?>
                          <?php if($asset->status->id == 1): ?>
                            <span class="label label-success">
                          <?php elseif($asset->status->id == 2): ?>
                            <span class="label label-info">
                          <?php elseif($asset->status->id == 3 || $asset->status->id == 4): ?>
                            <span class="label label-warning">
                          <?php elseif($asset->status->id == 5 || $asset->status->id == 6): ?>
                            <span class="label label-danger">
                          <?php else: ?>
                            <span class="label label-default">
                          <?php endif; ?>
                          <?php echo e($asset->status->name); ?></span>
                        <?php else: ?>
                          <span class="label label-default">No Status</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="/assets/<?php echo e($asset->id); ?>/move" class="btn btn-primary"><span class="fa fa-send" aria-hidden="true"></span> <b>Move</b></a>
                        <a href="/assets/<?php echo e($asset->id); ?>/history" class="btn btn-primary"><span class="fa fa-calendar" aria-hidden="true"></span> <b>History</b></a>
                        <a href="<?php echo e(route('tickets.create', ['asset_id' => $asset->id])); ?>" class="btn btn-warning"><span class="fa fa-ticket" aria-hidden="true"></span> <b>Tickets</b></a>
                        <a href="/assets/<?php echo e($asset->id); ?>/edit" class="btn btn-primary"><span class="fa fa-pencil" aria-hidden="true"></span> <b>Edit</b></a>
                        <form method="POST" action="<?php echo e(url('assets/' . $asset->id)); ?>" style="display:inline-block; margin-left:6px;" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                          <?php echo e(csrf_field()); ?>

                          <?php echo e(method_field('DELETE')); ?>

                          <button type="submit" class="btn btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> <b>Delete</b></button>
                        </form>
                      </div>
                    </td>
                    <td><?php echo e($asset->supplier ? $asset->supplier->name : 'N/A'); ?></td>
                    <td><?php echo e($asset->purchase_date); ?></td>
                    <td><?php echo e($asset->warranty_months); ?></td>
                    <td><?php echo e($asset->warranty_type ? $asset->warranty_type->name : 'N/A'); ?></td>
                  </div>
                </tr>
                <?php $age = null; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
          
          <?php if(method_exists($assets, 'links')): ?>
            <div class="mt-2">
              <?php echo e($assets->links()); ?>

            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <script>
  $(document).ready(function() {
  var table = $('#table').DataTable( {
    responsive: true,
    dom: 'l<"clear">Bfrtip',
    pageLength: 25,
    lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
    buttons: [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        className: 'btn btn-success btn-sm',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
        }
      },
      {
        extend: 'csv',
        text: '<i class="fa fa-file-text-o"></i> CSV',
        className: 'btn btn-info btn-sm',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
        }
      },
      {
        extend: 'pdf',
        text: '<i class="fa fa-file-pdf-o"></i> PDF',
        className: 'btn btn-danger btn-sm',
        orientation: 'landscape',
        pageSize: 'A4',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7]
        }
      },
      {
        extend: 'copy',
        text: '<i class="fa fa-copy"></i> Copy',
        className: 'btn btn-default btn-sm',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
        }
      }
    ],
    columns: [
      null,
      { "visible": false },
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      { "visible": false },
      { "visible": false },
      { "visible": false },
      { "visible": false }
    ], 
    columnDefs: [{
      orderable: false, targets: 8
    }],
    language: {
      lengthMenu: "Show _MENU_ assets per page",
      info: "Showing _START_ to _END_ of _TOTAL_ assets",
      infoEmpty: "No assets to show",
      infoFiltered: "(filtered from _MAX_ total assets)",
      search: "Quick Search:",
      paginate: {
        first: '<i class="fa fa-angle-double-left"></i>',
        previous: '<i class="fa fa-angle-left"></i>',
        next: '<i class="fa fa-angle-right"></i>',
        last: '<i class="fa fa-angle-double-right"></i>'
      }
    },
    drawCallback: function() {
      // Update asset count badge
      var info = table.page.info();
      $('#assetCount').text(info.recordsDisplay + ' Assets');
    }
  } );
    // Export filtered results button
    $('#exportFiltered').on('click', function() {
      table.button('.buttons-excel').trigger();
    });

    // Get the model, location, division and status columns' div IDs for each row.
    // If it is clicked on, then the datatable will filter that.
    <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      // Model
      var model = (function() {
        var x = '#model' + <?php echo e($asset->id); ?>;
        return x;
      });
      $(model()).click(function () {
        <?php if($asset->model && $asset->model->manufacturer): ?>
        table.search( "<?php echo e($asset->model->manufacturer->name); ?> - <?php echo e($asset->model->asset_model); ?>" ).draw();
        <?php elseif($asset->model): ?>
        table.search( "<?php echo e($asset->model->asset_model); ?>" ).draw();
        <?php else: ?>
        table.search( "N/A" ).draw();
        <?php endif; ?>
      });

      // Location
      var location = (function() {
        var x = '#location' + <?php echo e($asset->id); ?>;
        return x;
      });
      $(location()).click(function () {
        <?php if($asset->movement && $asset->movement->location): ?>
        table.search( "<?php echo e($asset->movement->location->location_name); ?>" ).draw();
        <?php else: ?>
        table.search( "N/A" ).draw();
        <?php endif; ?>
      });

      // Division
      var division = (function() {
        var x = '#division' + <?php echo e($asset->id); ?>;
        return x;
      });
      $(division()).click(function () {
        <?php if($asset->division): ?>
        table.search( "<?php echo e($asset->division->name); ?>" ).draw();
        <?php else: ?>
        table.search( "N/A" ).draw();
        <?php endif; ?>
      });

      // Status
      var status = (function() {
        var x = '#status' + <?php echo e($asset->id); ?>;
        return x;
      });
      $(status()).click(function () {
        <?php if($asset->movement && $asset->movement->status): ?>
        table.search( "<?php echo e($asset->movement->status->name); ?>" ).draw();
        <?php else: ?>
        table.search( "N/A" ).draw();
        <?php endif; ?>
      });
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  } );

  // Filter by status when clicking stat cards
  window.filterByStatus = function(status) {
    var searchTerm = '';
    switch(status) {
      case 'deployed': searchTerm = 'Deployed'; break;
      case 'ready': searchTerm = 'Ready'; break;
      case 'repairs': searchTerm = 'Repairs'; break;
      case 'written-off': searchTerm = 'Written Off'; break;
      case 'all': searchTerm = ''; break;
    }
    table.search(searchTerm).draw();
  };

  // Enhanced box collapse button text toggle
  $('.box').on('expanded.boxwidget', function() {
    $(this).find('.btn-box-tool i').removeClass('fa-plus').addClass('fa-minus');
    $(this).find('.btn-box-tool').contents().last()[0].textContent = ' Collapse Filters';
  });
  $('.box').on('collapsed.boxwidget', function() {
    $(this).find('.btn-box-tool i').removeClass('fa-minus').addClass('fa-plus');
    $(this).find('.btn-box-tool').contents().last()[0].textContent = ' Expand Filters';
  });
  </script>
  <?php if(Session::has('status')): ?>
    <script>
      $(document).ready(function() {
        Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
  <?php endif; ?>

<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/assets/index.blade.php ENDPATH**/ ?>