

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
                <th>Invoice</th>
                <th>Order</th>
                <th>Total</th>
                <th>Division</th>
                <th>Supplier</th>
                <th>Invoiced Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <div>
                    <td><?php echo e($invoice->invoice_number); ?></td>
                    <td><?php echo e($invoice->order_number); ?></td>
                    <td>Rp<?php echo e($invoice->total); ?></td>
                    <td><?php echo e($invoice->division->name); ?></td>
                    <td><?php echo e($invoice->supplier->name); ?></td>
                    <td><?php echo e($invoice->invoiced_date); ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="/invoices/<?php echo e($invoice->id); ?>" class="btn btn-primary"><span class='fa fa-usd' aria-hidden='true'></span> <b>View</b></a>
                        <a href="/invoices/<?php echo e($invoice->id); ?>/edit" class="btn btn-primary"><span class='fa fa-pencil' aria-hidden='true'></span> <b>Edit</b></a>
                      </div>
                    </td>
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
          <h3 class="box-title">Create New Invoice</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('invoices')); ?>" enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'invoice_number')); ?>">
              <label for="invoice_number">Invoice Number</label>
              <input type="text"  name="invoice_number" class="form-control" value="<?php echo e(old('invoice_number')); ?>">
              <?php echo e(hasErrorForField($errors, 'invoice_number')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'order_number')); ?>">
              <label for="order_number">Order Number</label>
              <input type="text"  name="order_number" class="form-control" value="<?php echo e(old('order_number')); ?>">
              <?php echo e(hasErrorForField($errors, 'order_number')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'invoiced_date')); ?>">
              <label for="invoiced_date">Invoiced Date</label>
              <input type="date"  name="invoiced_date" class="form-control" value="<?php echo e(old('invoiced_date')); ?>">
              <?php echo e(hasErrorForField($errors, 'invoiced_date')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'total')); ?>">
              <label for="total">Invoice Total (Incl. VAT)</label>
              <div class="input-group">
                <div class="input-group-addon">R</div>
                <input type="text"  name="total" class="form-control" value="<?php echo e(old('total')); ?>">
              </div>
              <?php echo e(hasErrorForField($errors, 'total')); ?>

            </div>
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
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'supplier_id')); ?>">
              <label for="supplier_id">Supplier</label>
              <select class="form-control supplier_id" name="supplier_id">
                <option value = ""></option>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'supplier_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'file')); ?>">
              <label for="file">Upload Invoice (PDF Only)</label>
              <input type="file"  name="file" class="form-control">
              <?php echo e(hasErrorForField($errors, 'file')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Invoice</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
  $(document).ready(function() {
    $('#table').DataTable( {
        responsive: true,
        dom: 'l<"clear">Bfrtip',
        buttons: [
            {
              extend: 'excel',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5]
              }
            },
            {
              extend: 'csv',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5]
              }
            },
            {
              extend: 'copy',
              exportOptions: {
                columns: [0, 1, 2, 3, 4, 5]
              }
            }
        ],
        columnDefs: [ {
          orderable: false, targets: 6
        } ],
        order: [[ 5, "desc" ]]
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
      $(".supplier_id").select2();
      $(".division_id").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/invoices/index.blade.php ENDPATH**/ ?>