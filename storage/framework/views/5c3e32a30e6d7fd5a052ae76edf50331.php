

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Division</th>
                <th>Year</th>
                <th>Budget Total</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($budget->division->name); ?></td>
                    <td><?php echo e($budget->year); ?></td>
                    <td>R<?php echo e(number_format($budget->total,2)); ?></td>
                    <td><a href="/budgets/<?php echo e($budget->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a></td>
                  </div>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create Budget</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('budgets')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'division_id')); ?>">
              <label for="division_id">Division</label>
              <select class="form-control division_id" name="division_id">
                <option value = ""></option>
                <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($division->id); ?>"><?php echo e($division->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'division_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'year')); ?>">
              <label for="year">Year</label>
              <input type="text"  name="year" class="form-control" value="<?php echo e(old('year')); ?>">
              <?php echo e(hasErrorForField($errors, 'year')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'total')); ?>">
              <label for="total">Budget Total</label>
              <div class="input-group">
                <div class="input-group-addon">Rp</div>
                <input type="text"  name="total" class="form-control" value="<?php echo e(old('total')); ?>">
                <?php echo e(hasErrorForField($errors, 'total')); ?>

              </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Budget</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      $('#table').DataTable( {
        columnDefs: [ {
          orderable: false, targets: 3
        } ],
        order: [[ 1, "desc" ]]
      } );
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
<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".division_id").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/budgets/index.blade.php ENDPATH**/ ?>