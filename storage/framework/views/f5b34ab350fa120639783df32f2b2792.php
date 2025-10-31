

<?php $__env->startSection('main-content'); ?>




<?php echo $__env->make('components.page-header', [
    'title' => 'Edit PC Specification',
    'subtitle' => 'Update hardware specifications',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'PC Specifications', 'url' => url('pcspecs')],
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
                <i class="fa fa-info-circle"></i> <strong>PC Specification Information</strong>
                <div style="margin-top: 10px;">
                    <span style="margin-right: 20px;"><strong>Created:</strong> <?php echo e($pcspec->created_at ? $pcspec->created_at->format('M d, Y') : 'N/A'); ?></span>
                    <span><strong>Updated:</strong> <?php echo e($pcspec->updated_at ? $pcspec->updated_at->format('M d, Y') : 'N/A'); ?></span>
                </div>
            </div>

            
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-edit"></i> Edit PC Specification Details</h3>
                </div>
                <div class="box-body">
                    <form method="POST" action="/pcspecs/<?php echo e($pcspec->id); ?>" id="pcspec-form">
                        <?php echo e(method_field('PATCH')); ?>

                        <?php echo e(csrf_field()); ?>

                        
                        <fieldset>
                            <legend><span class="form-section-icon"><i class="fa fa-microchip"></i></span> Hardware Specifications</legend>
                            
                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'cpu')); ?>">
                                <label for="cpu">CPU / Processor <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-microchip"></i></span>
                                    <input type="text" name="cpu" id="cpu" class="form-control" value="<?php echo e(old('cpu', $pcspec->cpu)); ?>" placeholder="e.g., Intel Core i7-10700" required>
                                </div>
                                <small class="help-text">Enter CPU model, generation, and speed</small>
                                <?php echo e(hasErrorForField($errors, 'cpu')); ?>

                            </div>

                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ram')); ?>">
                                <label for="ram">RAM / Memory <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-memory"></i></span>
                                    <input type="text" name="ram" id="ram" class="form-control" value="<?php echo e(old('ram', $pcspec->ram)); ?>" placeholder="e.g., 16GB DDR4" required>
                                </div>
                                <small class="help-text">Enter RAM size and type (DDR3/DDR4/DDR5)</small>
                                <?php echo e(hasErrorForField($errors, 'ram')); ?>

                            </div>

                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'hdd')); ?>">
                                <label for="hdd">Storage (HDD/SSD) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-hdd"></i></span>
                                    <input type="text" name="hdd" id="hdd" class="form-control" value="<?php echo e(old('hdd', $pcspec->hdd)); ?>" placeholder="e.g., 512GB NVMe SSD" required>
                                </div>
                                <small class="help-text">Enter storage capacity and type (HDD/SSD/NVMe)</small>
                                <?php echo e(hasErrorForField($errors, 'hdd')); ?>

                            </div>
                        </fieldset>

                        <div class="form-actions" style="margin-top: 30px;">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> <b>Update PC Specification</b>
                            </button>
                            <a href="<?php echo e(url('pcspecs')); ?>" class="btn btn-default btn-lg">
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
                    <p style="font-size: 13px;">Changing this specification will affect all asset models and assets linked to it.</p>
                    
                    <hr>
                    
                    <ul style="margin-left: 20px; font-size: 13px;">
                        <li>Double-check specifications before saving</li>
                        <li>Use consistent formatting (e.g., "16GB DDR4")</li>
                        <li>Include generation for CPUs (i5-10400, not just i5)</li>
                        <li>Specify storage type (SSD vs HDD vs NVMe)</li>
                    </ul>
                </div>
            </div>

            
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="box-body">
                    <a href="<?php echo e(url('pcspecs')); ?>" class="btn btn-default btn-block">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                    <?php if(Route::has('pcspecs.show')): ?>
                    <a href="<?php echo e(route('pcspecs.show', $pcspec->id)); ?>" class="btn btn-primary btn-block">
                        <i class="fa fa-eye"></i> View Specification Details
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-check-circle"></i> Best Practices</h3>
                </div>
                <div class="box-body">
                    <p style="font-size: 12px; margin-bottom: 10px;"><strong>Format Examples:</strong></p>
                    <ul style="margin-left: 20px; font-size: 12px;">
                        <li><i class="fa fa-check text-success"></i> <strong>CPU:</strong> Intel Core i7-10700 @ 2.9GHz</li>
                        <li><i class="fa fa-check text-success"></i> <strong>RAM:</strong> 16GB DDR4 2666MHz</li>
                        <li><i class="fa fa-check text-success"></i> <strong>Storage:</strong> 512GB NVMe SSD</li>
                    </ul>
                    
                    <hr style="margin: 15px 0;">
                    
                    <p style="font-size: 12px; margin-bottom: 5px;"><strong>Performance Categories:</strong></p>
                    <ul style="margin-left: 20px; font-size: 11px;">
                        <li><strong>Basic:</strong> i3 / 4-8GB / 256GB SSD</li>
                        <li><strong>Standard:</strong> i5 / 8-16GB / 512GB SSD</li>
                        <li><strong>Performance:</strong> i7 / 16-32GB / 1TB SSD</li>
                        <li><strong>Workstation:</strong> i9/Xeon / 32GB+ / 2TB+ NVMe</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


Core i3 5123

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Form validation
        $('#pcspec-form').on('submit', function(e) {
            if ($('#cpu').val().trim() === '' || $('#ram').val().trim() === '' || $('#hdd').val().trim() === '') {
                alert('Please fill in all required fields');
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/pcspecs/edit.blade.php ENDPATH**/ ?>