

<?php $__env->startSection('page_title'); ?>
    Asset Statuses Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Manage asset status labels and lifecycle stages
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-info-circle"></i> Asset Statuses
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addStatusModal">
                        <i class="fa fa-plus"></i> Add Status
                    </button>
                    <a href="<?php echo e(route('system-settings.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Settings
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="statusesTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($status->id); ?></td>
                                <td>
                                    <span class="badge badge-primary">
                                        <?php echo e($status->name); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info btn-edit-status" 
                                                data-id="<?php echo e($status->id); ?>"
                                                data-name="<?php echo e($status->name); ?>"
                                                data-toggle="modal" data-target="#editStatusModal">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="<?php echo e(route('status.destroy', $status->id)); ?>" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this status?')">
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
                                    <p class="text-muted">No asset statuses found. <a href="#" data-toggle="modal" data-target="#addStatusModal">Create your first one</a>.</p>
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

<!-- Add Status Modal -->
<div class="modal fade" id="addStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('status.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add New Asset Status</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Status Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               placeholder="e.g., Active, In Repair, Disposed, Available">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="editStatusForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Asset Status</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Status Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#statusesTable').DataTable({
        columnDefs: [{
            orderable: false, targets: 2 // Actions column
        }],
        order: [[ 0, "asc" ]]
    });

    // Handle edit status modal
    $('.btn-edit-status').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_name').val(name);
        $('#editStatusForm').attr('action', '<?php echo e(route("status.update", ":status")); ?>'.replace(':status', id));
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/system-settings/asset-configs/statuses.blade.php ENDPATH**/ ?>