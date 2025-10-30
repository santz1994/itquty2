

<?php $__env->startSection('main-content'); ?>




<?php echo $__env->make('components.page-header', [
    'title' => $pageTitle ?? 'PC Specifications',
    'subtitle' => 'Manage computer hardware specifications',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'PC Specifications']
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">

    
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

    <div class="row">
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-microchip"></i> PC Specifications</h3>
                    <span class="count-badge"><?php echo e(count($pcspecs)); ?></span>
                </div>
                <div class="box-body">
                    <table id="table" class="table table-enhanced table-striped table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-microchip"></i> CPU</th>
                                <th><i class="fa fa-memory"></i> RAM</th>
                                <th><i class="fa fa-hdd"></i> Storage</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $pcspecs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pcspec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong><?php echo e($pcspec->cpu); ?></strong></td>
                                    <td><?php echo e($pcspec->ram); ?></td>
                                    <td><?php echo e($pcspec->hdd); ?></td>
                                    <td>
                                        <a href="/pcspecs/<?php echo e($pcspec->id); ?>/edit" class="btn btn-xs btn-warning" title="Edit">
                                            <i class='fa fa-edit'></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center empty-state">
                                        <i class="fa fa-microchip fa-3x" style="opacity: 0.3; margin-bottom: 15px;"></i>
                                        <p>No PC specifications found.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-plus-circle"></i> Add PC Spec</h3>
                </div>
                <div class="box-body">
                    <form method="POST" action="<?php echo e(url('pcspecs')); ?>" id="pcspec-form">
                        <?php echo e(csrf_field()); ?>

                        
                        <fieldset>
                            <legend><span class="form-section-icon"><i class="fa fa-microchip"></i></span> Hardware Specs</legend>
                            
                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'cpu')); ?>">
                                <label for="cpu">CPU / Processor <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-microchip"></i></span>
                                    <input type="text" name="cpu" id="cpu" class="form-control" value="<?php echo e(old('cpu')); ?>" placeholder="e.g., Intel Core i7-10700" required>
                                </div>
                                <small class="help-text">Enter CPU model and speed</small>
                                <?php echo e(hasErrorForField($errors, 'cpu')); ?>

                            </div>

                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ram')); ?>">
                                <label for="ram">RAM / Memory <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-memory"></i></span>
                                    <input type="text" name="ram" id="ram" class="form-control" value="<?php echo e(old('ram')); ?>" placeholder="e.g., 16GB DDR4" required>
                                </div>
                                <small class="help-text">Enter RAM size and type</small>
                                <?php echo e(hasErrorForField($errors, 'ram')); ?>

                            </div>

                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'hdd')); ?>">
                                <label for="hdd">Storage (HDD/SSD) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-hdd"></i></span>
                                    <input type="text" name="hdd" id="hdd" class="form-control" value="<?php echo e(old('hdd')); ?>" placeholder="e.g., 512GB NVMe SSD" required>
                                </div>
                                <small class="help-text">Enter storage capacity and type</small>
                                <?php echo e(hasErrorForField($errors, 'hdd')); ?>

                            </div>
                        </fieldset>

                        <div class="form-group" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-plus"></i> <b>Add PC Spec</b>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-lightbulb"></i> Guidelines</h3>
                </div>
                <div class="box-body">
                    <ul style="margin-left: 20px; font-size: 13px;">
                        <li><i class="fa fa-check text-success"></i> Be specific with model numbers</li>
                        <li><i class="fa fa-check text-success"></i> Include generation/speed for CPU</li>
                        <li><i class="fa fa-check text-success"></i> Specify RAM type (DDR3/DDR4/DDR5)</li>
                        <li><i class="fa fa-check text-success"></i> Include storage type (HDD/SSD/NVMe)</li>
                    </ul>
                    
                    <p style="margin-top: 15px;"><strong>Examples:</strong></p>
                    <ul style="margin-left: 20px; font-size: 12px;">
                        <li><strong>CPU:</strong> Intel Core i7-10700 @ 2.9GHz</li>
                        <li><strong>RAM:</strong> 16GB DDR4 2666MHz</li>
                        <li><strong>Storage:</strong> 512GB NVMe SSD</li>
                    </ul>
                </div>
            </div>

            
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle"></i> Quick Tips</h3>
                </div>
                <div class="box-body">
                    <p style="font-size: 12px;">
                        <i class="fa fa-lightbulb text-warning"></i> <strong>Common Specs:</strong>
                    </p>
                    <ul style="margin-left: 20px; font-size: 11px;">
                        <li><strong>Basic:</strong> i3/4GB/256GB</li>
                        <li><strong>Standard:</strong> i5/8GB/512GB</li>
                        <li><strong>Performance:</strong> i7/16GB/1TB</li>
                        <li><strong>Workstation:</strong> i9/32GB/2TB</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


Successfully created

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Enhanced DataTable with export buttons
        var table = $('#table').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: { columns: [0, 1, 2] }
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-csv"></i> CSV',
                    className: 'btn btn-info btn-sm',
                    exportOptions: { columns: [0, 1, 2] }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: { columns: [0, 1, 2] }
                },
                {
                    extend: 'copy',
                    text: '<i class="fa fa-copy"></i> Copy',
                    className: 'btn btn-default btn-sm',
                    exportOptions: { columns: [0, 1, 2] }
                }
            ],
            columnDefs: [{ orderable: false, targets: 3 }],
            order: [[0, "asc"]],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search specifications...",
                lengthMenu: "Show _MENU_ specifications",
                info: "Showing _START_ to _END_ of _TOTAL_ specifications",
                paginate: {
                    first: '<i class="fa fa-angle-double-left"></i>',
                    last: '<i class="fa fa-angle-double-right"></i>',
                    next: '<i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                }
            }
        });

        // Move export buttons to header
        table.buttons().container().appendTo($('.box-header .box-title').parent());

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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/pcspecs/index.blade.php ENDPATH**/ ?>