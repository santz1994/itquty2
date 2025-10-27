

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', [
    'title' => 'Assets',
    'subtitle' => 'Manage all IT assets and equipment',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Assets']
    ],
    'actions' => '<a href="'.route('assets.create').'" class="btn btn-primary">
        <i class="fa fa-plus"></i> Create New Asset
    </a>
    <a href="'.route('assets.import-form').'" class="btn btn-info">
        <i class="fa fa-upload"></i> Import
    </a>
    <a href="'.route('assets.export').'" class="btn btn-success">
        <i class="fa fa-download"></i> Export
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-purple">
        <div class="inner">
          <h3><?php echo e($totalAssets); ?></h3>
          <p>Total</p>
        </div>
        <div class="icon">
          <i class="fa fa-tags"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3><?php echo e($deployed); ?></h3>
          <p>Deployed</p>
        </div>
        <div class="icon">
          <i class="fa fa-check-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-green">
        <div class="inner">
          <h3><?php echo e($readyToDeploy); ?></h3>
          <p>Ready</p>
        </div>
        <div class="icon">
          <i class="fa fa-plus-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?php echo e($repairs); ?></h3>
          <p>Repairs</p>
        </div>
        <div class="icon">
          <i class="fa fa-question-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?php echo e($writtenOff); ?></h3>
          <p>Written Off</p>
        </div>
        <div class="icon">
          <i class="fa fa-times-circle"></i>
        </div>
      </div>
      <!-- /.info-box -->
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
    <div class="col-md-12 col-xs-12 col-lg-12">
      <div class="box box-primary">
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
                        <?php if($asset->movement && $asset->movement->status): ?>
                          <?php if($asset->movement->status->id == 1): ?>
                            <span class="label label-success">
                          <?php elseif($asset->movement->status->id == 2): ?>
                            <span class="label label-info">
                          <?php elseif($asset->movement->status->id == 3 || $asset->movement->status->id == 4): ?>
                            <span class="label label-warning">
                          <?php elseif($asset->movement->status->id == 5 || $asset->movement->status->id == 6): ?>
                            <span class="label label-danger">
                          <?php else: ?>
                            <span class="label label-default">
                          <?php endif; ?>
                          <?php echo e($asset->movement->status->name); ?></span>
                        <?php else: ?>
                          <span class="label label-default">No Status</span>
                        <?php endif; ?>
                      </div>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="/assets/<?php echo e($asset->id); ?>/move" class="btn btn-primary"><span class="fa fa-send" aria-hidden="true"></span> <b>Move</b></a>
                        <a href="/assets/<?php echo e($asset->id); ?>/history" class="btn btn-primary"><span class="fa fa-calendar" aria-hidden="true"></span> <b>History</b></a>
                        <a href="/assets/<?php echo e($asset->id); ?>/ticket-history" class="btn btn-warning"><span class="fa fa-ticket" aria-hidden="true"></span> <b>Tickets</b></a>
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
    // Default page length and length menu. Note: DataTables paginates client-side
    // only the rows present in the HTML. To let DataTables handle all rows client-side,
    // request all rows by adding `?all=1` to the assets index URL (controller supports this).
    pageLength: 25,
    lengthMenu: [[10,25,50,100,-1],[10,25,50,100,'All']],
        buttons: [
            {
              extend: 'excel',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
              }
            },
            {
              extend: 'csv',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12]
              }
            },
            {
              extend: 'copy',
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
        ], columnDefs: [{
          orderable: false, targets: 8
        }]
    } );
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