

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
            <form method="POST" action="/invoices/<?php echo e($invoice->id); ?>" enctype="multipart/form-data">
              <?php echo e(method_field('PATCH')); ?>

              <?php echo e(csrf_field()); ?>

              <div class="form-group <?php echo e(hasErrorForClass($errors, 'invoice_number')); ?>">
                <label for="invoice_number">Invoice Number</label>
                <input type="text"  name="invoice_number" class="form-control" value="<?php echo e($invoice->invoice_number); ?>">
                <?php echo e(hasErrorForField($errors, 'invoice_number')); ?>

              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'order_number')); ?>">
                <label for="order_number">Order Number</label>
                <input type="text"  name="order_number" class="form-control" value="<?php echo e($invoice->order_number); ?>">
                <?php echo e(hasErrorForField($errors, 'order_number')); ?>

              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'invoiced_date')); ?>">
                <label for="invoiced_date">Invoiced Date</label>
                <input type="date"  name="invoiced_date" class="form-control" value="<?php echo e($invoice->invoiced_date); ?>">
                <?php echo e(hasErrorForField($errors, 'invoiced_date')); ?>

              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'total')); ?>">
                <label for="total">Invoice Total (Incl. VAT)</label>
                <div class="input-group">
                  <div class="input-group-addon">Rp</div>
                  <input type="text"  name="total" class="form-control" value="<?php echo e($invoice->total); ?>">
                  <?php echo e(hasErrorForField($errors, 'total')); ?>

                </div>
              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'division_id')); ?>">
                <label for="asset_model_id">Division</label>
                <select class="form-control division_id" name="division_id">
                  <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option
                      <?php if($invoice->division_id == $division->id): ?>
                        selected
                      <?php endif; ?>
                    value="<?php echo e($division->id); ?>"><?php echo e($division->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php echo e(hasErrorForField($errors, 'division_id')); ?>

              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'supplier_id')); ?>">
                <label for="supplier_id">Supplier</label>
                <select class="form-control supplier_id" name="supplier_id">
                  <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option
                      <?php if($invoice->supplier_id == $supplier->id): ?>
                        selected
                      <?php endif; ?>
                    value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php echo e(hasErrorForField($errors, 'supplier_id')); ?>

              </div>
              <div class="form-group <?php echo e(hasErrorForClass($errors, 'file')); ?>">
                <label for="file">Upload Invoice (PDF Only)</label>
                <input type="file" name="file" class="form-control">
                <?php echo e(hasErrorForField($errors, 'file')); ?>

              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-primary"><b>Edit Invoice</b></button>
              </div>
            </form>
            <div class="text-center"><a class="btn btn-primary" href="<?php echo e(URL::previous()); ?>">Back</a></div>
          </div>
        </div>
      </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".division_id").select2();
      $(".supplier_id").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\invoices\edit.blade.php ENDPATH**/ ?>