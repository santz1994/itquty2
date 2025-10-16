

<?php $__env->startSection('page_title'); ?>
    Canned Fields Management
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page_description'); ?>
    Manage predefined ticket response templates
<?php $__env->stopSection(); ?>

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-text-width"></i> Canned Fields
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCannedFieldModal">
                        <i class="fa fa-plus"></i> Add Canned Field
                    </button>
                    <a href="<?php echo e(route('system-settings.index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Settings
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="cannedFieldsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $cannedFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($field->id); ?></td>
                                <td><?php echo e($field->title); ?></td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="<?php echo e($field->content); ?>">
                                        <?php echo e(\Illuminate\Support\Str::limit($field->content, 50)); ?>

                                    </div>
                                </td>
                                <td>
                                    <span class="label label-info"><?php echo e($field->category ?? 'General'); ?></span>
                                </td>
                                <td>
                                    <span class="label label-<?php echo e($field->is_active ? 'success' : 'danger'); ?>">
                                        <?php echo e($field->is_active ? 'Active' : 'Inactive'); ?>

                                    </span>
                                </td>
                                <td><?php echo e($field->created_at->format('M d, Y')); ?></td>
                                <td>
                                    <div class="btn-group btn-group-xs">
                                        <button type="button" class="btn btn-info btn-edit-canned-field" 
                                                data-id="<?php echo e($field->id); ?>"
                                                data-title="<?php echo e($field->title); ?>"
                                                data-content="<?php echo e($field->content); ?>"
                                                data-category="<?php echo e($field->category); ?>"
                                                data-active="<?php echo e($field->is_active); ?>"
                                                data-toggle="modal" data-target="#editCannedFieldModal">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form method="POST" action="<?php echo e(route('tickets-canned-field.destroy', $field->id)); ?>" 
                                              style="display: inline-block;"
                                              onsubmit="return confirm('Are you sure you want to delete this canned field?')">
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
                                <td colspan="7" class="text-center">
                                    <p class="text-muted">No canned fields found. <a href="#" data-toggle="modal" data-target="#addCannedFieldModal">Create your first one</a>.</p>
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

<!-- Add Canned Field Modal -->
<div class="modal fade" id="addCannedFieldModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('tickets-canned-field.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add New Canned Field</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content <span class="text-red">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="5" required 
                                  placeholder="Enter the template content..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="General">General</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                            <option value="Network">Network</option>
                            <option value="Account">Account</option>
                            <option value="Resolution">Resolution</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" value="1" checked> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Canned Field</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Canned Field Modal -->
<div class="modal fade" id="editCannedFieldModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" id="editCannedFieldForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Edit Canned Field</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">Title <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_content">Content <span class="text-red">*</span></label>
                        <textarea class="form-control" id="edit_content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Category</label>
                        <select class="form-control" id="edit_category" name="category">
                            <option value="General">General</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                            <option value="Network">Network</option>
                            <option value="Account">Account</option>
                            <option value="Resolution">Resolution</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="edit_is_active" name="is_active" value="1"> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Canned Field</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('javascript'); ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#cannedFieldsTable').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[ 0, "desc" ]]
    });

    // Handle edit canned field modal
    $('.btn-edit-canned-field').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        var content = $(this).data('content');
        var category = $(this).data('category');
        var isActive = $(this).data('active');

        $('#edit_title').val(title);
        $('#edit_content').val(content);
        $('#edit_category').val(category);
        $('#edit_is_active').prop('checked', isActive == 1);
        
        $('#editCannedFieldForm').attr('action', '<?php echo e(url("/tickets-canned-field")); ?>/' + id);
    });

    // Auto-resize textareas
    $('textarea').each(function() {
        this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
    }).on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/system-settings/ticket-configs/canned-fields.blade.php ENDPATH**/ ?>