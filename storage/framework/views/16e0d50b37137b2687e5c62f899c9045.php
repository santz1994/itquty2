

<?php $__env->startSection('page_title'); ?>
    Suppliers Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Manage vendor and supplier information
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        
        <!-- Quick Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Suppliers</span>
                        <span class="info-box-number"><?php echo e($suppliers->count()); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-truck"></i> Suppliers
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSupplierModal">
                        <i class="fa fa-plus"></i> Add Supplier
                    </button>
                    <a href="<?php echo e(route('system-settings.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Settings
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="suppliersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Supplier Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($supplier->id); ?></td>
                                <td>
                                    <span class="badge badge-success">
                                        <?php echo e($supplier->name); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info btn-edit-supplier" 
                                                data-id="<?php echo e($supplier->id); ?>"
                                                data-name="<?php echo e($supplier->name); ?>"
                                                data-toggle="modal" data-target="#editSupplierModal">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="<?php echo e(route('suppliers.destroy', $supplier->id)); ?>" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this supplier?')">
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
                                <td colspan="3" class="text-center">
                                    <p class="text-muted">No suppliers found. <a href="#" data-toggle="modal" data-target="#addSupplierModal">Add your first supplier</a>.</p>
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('suppliers.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add New Supplier</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Supplier Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               placeholder="e.g., Dell Technologies, Microsoft, Local Vendor">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="editSupplierForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Supplier</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Supplier Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#suppliersTable').DataTable({
        columnDefs: [{
            orderable: false, targets: 2 // Actions column
        }],
        order: [[ 0, "asc" ]]
    });

    // Handle edit supplier modal
    $('.btn-edit-supplier').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_name').val(name || '');
        $('#editSupplierForm').attr('action', '<?php echo e(route("suppliers.update", ":supplier")); ?>'.replace(':supplier', id));
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/system-settings/asset-configs/suppliers.blade.php ENDPATH**/ ?>