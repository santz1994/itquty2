

<?php $__env->startSection('page_title'); ?>
    Invoices Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Manage invoice records and financial tracking
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-file-text"></i> Invoices
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addInvoiceModal">
                        <i class="fa fa-plus"></i> Add Invoice
                    </button>
                    <a href="<?php echo e(route('system-settings.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Settings
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="invoicesTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice Number</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($invoice->id); ?></td>
                                <td>
                                    <strong><?php echo e($invoice->invoice_number); ?></strong>
                                    <?php if($invoice->po_number): ?>
                                        <br><small class="text-muted">PO: <?php echo e($invoice->po_number); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($invoice->supplier->name ?? '-'); ?></td>
                                <td><?php echo e($invoice->invoice_date ? $invoice->invoice_date->format('M d, Y') : '-'); ?></td>
                                <td>
                                    <?php if($invoice->total_amount): ?>
                                        <strong>$<?php echo e(number_format($invoice->total_amount, 2)); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php switch($invoice->status):
                                        case ('paid'): ?>
                                            <span class="label label-success">Paid</span>
                                            <?php break; ?>
                                        <?php case ('pending'): ?>
                                            <span class="label label-warning">Pending</span>
                                            <?php break; ?>
                                        <?php case ('overdue'): ?>
                                            <span class="label label-danger">Overdue</span>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <span class="label label-default"><?php echo e(ucfirst($invoice->status ?? 'Unknown')); ?></span>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <span class="badge bg-blue"><?php echo e($invoice->items_count ?? 0); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-info btn-edit-invoice" 
                                                data-id="<?php echo e($invoice->id); ?>"
                                                data-number="<?php echo e($invoice->invoice_number); ?>"
                                                data-po="<?php echo e($invoice->po_number); ?>"
                                                data-supplier="<?php echo e($invoice->supplier_id); ?>"
                                                data-date="<?php echo e($invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : ''); ?>"
                                                data-amount="<?php echo e($invoice->total_amount); ?>"
                                                data-status="<?php echo e($invoice->status); ?>"
                                                data-notes="<?php echo e($invoice->notes); ?>"
                                                data-toggle="modal" data-target="#editInvoiceModal">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <a href="<?php echo e(route('invoices.show', $invoice->id)); ?>" class="btn btn-success">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('invoices.destroy', $invoice->id)); ?>" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this invoice?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <p class="text-muted">No invoices found. <a href="#" data-toggle="modal" data-target="#addInvoiceModal">Add your first invoice</a>.</p>
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

<!-- Add Invoice Modal -->
<div class="modal fade" id="addInvoiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('invoices.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add New Invoice</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_number">Invoice Number <span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="po_number">PO Number</label>
                                <input type="text" class="form-control" id="po_number" name="po_number">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier_id">Supplier <span class="text-red">*</span></label>
                                <select class="form-control" id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="invoice_date">Invoice Date <span class="text-red">*</span></label>
                                <input type="date" class="form-control" id="invoice_date" name="invoice_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="total_amount">Total Amount</label>
                                <input type="number" class="form-control" id="total_amount" name="total_amount" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="editInvoiceForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Invoice</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_invoice_number">Invoice Number <span class="text-red">*</span></label>
                                <input type="text" class="form-control" id="edit_invoice_number" name="invoice_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_po_number">PO Number</label>
                                <input type="text" class="form-control" id="edit_po_number" name="po_number">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_supplier_id">Supplier <span class="text-red">*</span></label>
                                <select class="form-control" id="edit_supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_invoice_date">Invoice Date <span class="text-red">*</span></label>
                                <input type="date" class="form-control" id="edit_invoice_date" name="invoice_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_total_amount">Total Amount</label>
                                <input type="number" class="form-control" id="edit_total_amount" name="total_amount" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="paid">Paid</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_notes">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#invoicesTable').DataTable({
        columnDefs: [{
            // Actions column is the last column (index 7)
            orderable: false, targets: 7
        }],
        order: [[ 0, "asc" ]]
    });

    // Handle edit invoice modal
    $('.btn-edit-invoice').on('click', function() {
        var id = $(this).data('id');
        var number = $(this).data('number');
        var supplierId = $(this).data('supplier');
        var amount = $(this).data('amount');
        var date = $(this).data('date');
        var due = $(this).data('due');
        var description = $(this).data('description');

        $('#edit_invoice_number').val(number || '');
        $('#edit_supplier_id').val(supplierId || '');
        $('#edit_amount').val(amount || '');
        $('#edit_invoice_date').val(date || '');
        $('#edit_due_date').val(due || '');
        $('#edit_description').val(description || '');
        
        $('#editInvoiceForm').attr('action', '<?php echo e(route("invoices.update", ":invoice")); ?>'.replace(':invoice', id));
    });
});
</script>

<?php if(Session::has('status')): ?>
    <div class="alert alert-success" style="margin-top:10px;">
        <?php echo e(Session::get('message')); ?>

    </div>
    <script>
        $(document).ready(function() {
            Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
        });
    </script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\system-settings\asset-configs\invoices.blade.php ENDPATH**/ ?>