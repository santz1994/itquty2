

<?php $__env->startSection('main-content'); ?>




<?php echo $__env->make('components.page-header', [
    'title' => 'Edit Manufacturer',
    'subtitle' => 'Update manufacturer information',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Manufacturers', 'url' => url('manufacturers')],
        ['label' => 'Edit']
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-8">
            
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-check-circle"></i> <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i> Validation Error!</h4>
                    <ul style="margin-bottom: 0;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <div class="alert alert-warning">
                <i class="fa fa-info-circle"></i> <strong>Manufacturer Information</strong>
                <div style="margin-top: 10px;">
                    <span style="margin-right: 20px;"><strong>Created:</strong> <?php echo e($manufacturer->created_at->format('M d, Y')); ?></span>
                    <span><strong>Updated:</strong> <?php echo e($manufacturer->updated_at->format('M d, Y')); ?></span>
                </div>
            </div>

            
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-edit"></i> Edit Manufacturer Details</h3>
                </div>
                <div class="box-body">
                    <form method="POST" action="/manufacturers/<?php echo e($manufacturer->id); ?>" id="manufacturer-form">
                        <?php echo e(method_field('PATCH')); ?>

                        <?php echo e(csrf_field()); ?>

                        
                        <fieldset>
                            <legend><span class="form-section-icon"><i class="fa fa-industry"></i></span> Manufacturer Information</legend>
                            
                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'name')); ?>">
                                <label for="name">Manufacturer Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-industry"></i></span>
                                    <input type="text" name="name" id="name" class="form-control" value="<?php echo e(old('name', $manufacturer->name)); ?>" placeholder="e.g., Dell Technologies" required>
                                </div>
                                <small class="help-text">Use the official company name as registered</small>
                                <?php echo e(hasErrorForField($errors, 'name')); ?>

                            </div>
                        </fieldset>

                        <div class="form-actions" style="margin-top: 30px;">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> <b>Update Manufacturer</b>
                            </button>
                            <a href="<?php echo e(url('manufacturers')); ?>" class="btn btn-default btn-lg">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Edit Tips</h3>
                </div>
                <div class="box-body">
                    <p><i class="fa fa-info-circle text-info"></i> <strong>Impact Warning:</strong></p>
                    <p style="font-size: 13px;">Changing the manufacturer name will affect all assets associated with this manufacturer.</p>
                    
                    <hr>
                    
                    <ul style="margin-left: 20px; font-size: 13px;">
                        <li>Double-check spelling before saving</li>
                        <li>Use official company names</li>
                        <li>Avoid abbreviations when possible</li>
                        <li>Include corporate suffixes (Inc., Ltd., Corp., etc.) if applicable</li>
                    </ul>
                </div>
            </div>

            
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="box-body">
                    <a href="<?php echo e(url('manufacturers')); ?>" class="btn btn-default btn-block">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                    <?php if(Route::has('manufacturers.show')): ?>
                    <a href="<?php echo e(route('manufacturers.show', $manufacturer->id)); ?>" class="btn btn-primary btn-block">
                        <i class="fa fa-eye"></i> View Manufacturer Details
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-check-circle"></i> Best Practices</h3>
                </div>
                <div class="box-body">
                    <ul style="margin-left: 20px; font-size: 13px;">
                        <li><i class="fa fa-check text-success"></i> Use title case (Dell Technologies, not DELL TECHNOLOGIES)</li>
                        <li><i class="fa fa-check text-success"></i> Include full legal name</li>
                        <li><i class="fa fa-check text-success"></i> Check for existing duplicates</li>
                        <li><i class="fa fa-check text-success"></i> Keep naming consistent across system</li>
                    </ul>
                    
                    <p style="margin-top: 10px; font-size: 12px;"><strong>Examples:</strong></p>
                    <ul style="margin-left: 20px; font-size: 12px;">
                        <li>Dell Technologies</li>
                        <li>HP Inc.</li>
                        <li>Lenovo Group Limited</li>
                        <li>Microsoft Corporation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Form validation
        $('#manufacturer-form').on('submit', function(e) {
            if ($('#name').val().trim() === '') {
                alert('Please enter the manufacturer name');
                $('#name').focus();
                return false;
            }
            return true;
        });

        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });

    <?php if(Session::has('status')): ?>
        $(document).ready(function() {
            Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
        });
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/manufacturers/edit.blade.php ENDPATH**/ ?>