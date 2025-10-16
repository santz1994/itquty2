

<?php $__env->startSection('main-content'); ?>
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
    <div class="col-md-12 col-xs-12 col-lg-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Assets</h3>
        </div>
        <div class="box-body">
          <p class="pull-right"><a href="assets/create"><button type="button" class="btn btn-default" name="create-new-asset" data-toggle="tooltip" data-original-title="Create New Asset"><span class='fa fa-plus' aria-hidden='true'></span> <b>Create New Asset</b></button></a></p>
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Tag</th>
                <th>Asset Type</th>
                <th>S/N</th>
                <th>Age</th>
                <th>Model</th>
                <th>Location</th>
                <th>Division</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Supplier</th>
                <th>Purchase Date</th>
                <th>Warranty Months</th>
                <th>Warranty Type</th>
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
                    <td><?php echo e($asset->model->asset_type->type_name); ?></td>
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
                        <?php echo e($asset->model->manufacturer->name); ?> - <?php echo e($asset->model->asset_model); ?>

                      </div>
                    </td>
                    <td>
                      <div id="location<?php echo e($asset->id); ?>" class="hover-pointer">
                        <?php echo e($asset->movement->location->location_name); ?>

                      </div>
                    </td>
                    <td>
                      <div id="division<?php echo e($asset->id); ?>" class="hover-pointer">
                        <?php echo e($asset->division->name); ?>

                      </div>
                    </td>
                    <td>
                      <div id="status<?php echo e($asset->id); ?>" class="hover-pointer">
                        <?php if($asset->movement->status->id == 1): ?>
                          <span class="label label-success">
                        <?php elseif($asset->movement->status->id == 2): ?>
                          <span class="label label-info">
                        <?php elseif($asset->movement->status->id == 3 || $asset->movement->status->id == 4): ?>
                          <span class="label label-warning">
                        <?php elseif($asset->movement->status->id == 5 || $asset->movement->status->id == 6): ?>
                          <span class="label label-danger">
                        <?php endif; ?>
                        <?php echo e($asset->movement->status->name); ?></span>
                      </div>
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="/assets/<?php echo e($asset->id); ?>/move" class="btn btn-primary"><span class="fa fa-send" aria-hidden="true"></span> <b>Move</b></a>
                        <a href="/assets/<?php echo e($asset->id); ?>/history" class="btn btn-primary"><span class="fa fa-calendar" aria-hidden="true"></span> <b>History</b></a>
                        <a href="/assets/<?php echo e($asset->id); ?>/ticket-history" class="btn btn-warning"><span class="fa fa-ticket" aria-hidden="true"></span> <b>Tickets</b></a>
                        <a href="/assets/<?php echo e($asset->id); ?>/edit" class="btn btn-primary"><span class="fa fa-pencil" aria-hidden="true"></span> <b>Edit</b></a>
                      </div>
                    </td>
                    <td><?php echo e($asset->supplier->name); ?></td>
                    <td><?php echo e($asset->purchase_date); ?></td>
                    <td><?php echo e($asset->warranty_months); ?></td>
                    <td><?php echo e($asset->warranty_type->name); ?></td>
                  </div>
                </tr>
                <?php $age = null; ?>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script>
  $(document).ready(function() {
    var table = $('#table').DataTable( {
        responsive: true,
        dom: 'l<"clear">Bfrtip',
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
        table.search( "<?php echo e($asset->model->manufacturer->name); ?> - <?php echo e($asset->model->asset_model); ?>" ).draw();
      });

      // Location
      var location = (function() {
        var x = '#location' + <?php echo e($asset->id); ?>;
        return x;
      });
      $(location()).click(function () {
        table.search( "<?php echo e($asset->movement->location->location_name); ?>" ).draw();
      });

      // Division
      var division = (function() {
        var x = '#division' + <?php echo e($asset->id); ?>;
        return x;
      });
      $(division()).click(function () {
        table.search( "<?php echo e($asset->division->name); ?>" ).draw();
      });

      // Status
      var status = (function() {
        var x = '#status' + <?php echo e($asset->id); ?>;
        return x;
      });
      $(status()).click(function () {
        table.search( "<?php echo e($asset->movement->status->name); ?>" ).draw();
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
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/assets/index.blade.php ENDPATH**/ ?>