

<?php $__env->startSection('page_title'); ?>
    Divisions Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Manage organizational divisions and departments
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-building"></i> Divisions
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDivisionModal">
                        <i class="fa fa-plus"></i> Add Division
                    </button>
                    <a href="<?php echo e(route('system-settings.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Settings
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="divisionsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Division Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($division->id); ?></td>
                                <td>
                                    <span class="badge badge-warning">
                                        <?php echo e($division->name); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info btn-edit-division" 
                                                data-id="<?php echo e($division->id); ?>"
                                                data-name="<?php echo e($division->name); ?>"
                                                data-toggle="modal" data-target="#editDivisionModal">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="<?php echo e(route('divisions.destroy', $division->id)); ?>" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this division?')">
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
                                    <p class="text-muted">No divisions found. <a href="#" data-toggle="modal" data-target="#addDivisionModal">Create your first one</a>.</p>
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

<script>
$(document).ready(function() {
    $('#divisionsTable').DataTable({
        columnDefs: [{
            orderable: false, targets: 2 // Actions column
        }],
        order: [[ 0, "asc" ]]
    });

    // Handle edit division modal
    $('.btn-edit-division').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_name').val(name || '');
        $('#editDivisionForm').attr('action', '<?php echo e(route("divisions.update", ":division")); ?>'.replace(':division', id));
    });
});
</script>

<!-- Add Division Modal -->
<div class="modal fade" id="addDivisionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('divisions.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add New Division</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Division Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required 
                               placeholder="e.g., IT Department, Human Resources, Finance">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Division</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Division Modal -->
<div class="modal fade" id="editDivisionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="editDivisionForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Division</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Division Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Division</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/system-settings/asset-configs/divisions.blade.php ENDPATH**/ ?>