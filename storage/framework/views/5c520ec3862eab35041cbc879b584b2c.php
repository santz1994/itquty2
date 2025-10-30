

<?php $__env->startSection('main-content'); ?>




<?php echo $__env->make('components.page-header', [
    'title' => 'Budget Management',
    'subtitle' => 'Financial Planning & Tracking',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Budgets']
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

    <?php if(Session::has('status')): ?>
        <div class="alert alert-<?php echo e(Session::get('status') == 'success' ? 'success' : 'danger'); ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-<?php echo e(Session::get('status') == 'success' ? 'check-circle' : 'exclamation-triangle'); ?>"></i>
            <strong><?php echo e(Session::get('title')); ?></strong> - <?php echo e(Session::get('message')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-warning"></i> Validation Errors</h4>
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-money-bill-wave"></i> All Budgets
                        <span class="count-badge"><?php echo e($budgets->count()); ?></span>
                    </h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <table id="table" class="table table-bordered table-striped table-hover table-enhanced">
                        <thead>
                            <tr>
                                <th><i class="fa fa-sitemap"></i> Division</th>
                                <th><i class="fa fa-calendar"></i> Fiscal Year</th>
                                <th><i class="fa fa-money-bill-wave"></i> Budget Amount</th>
                                <th style="width: 150px;"><i class="fa fa-cog"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($budgets->count() > 0): ?>
                                <?php $__currentLoopData = $budgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $budget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong><?php echo e($budget->division->name); ?></strong></td>
                                        <td>
                                            <span class="badge bg-blue"><?php echo e($budget->year); ?></span>
                                        </td>
                                        <td>
                                            <strong style="color: #28a745; font-size: 14px;">
                                                R <?php echo e(number_format($budget->total, 2)); ?>

                                            </strong>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(url('budgets/' . $budget->id)); ?>" class="btn btn-xs btn-info" title="View Budget">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(url('budgets/' . $budget->id . '/edit')); ?>" class="btn btn-xs btn-warning" title="Edit Budget">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center empty-state" style="padding: 30px;">
                                        <i class="fa fa-money-bill-wave fa-3x" style="opacity: 0.3; margin-bottom: 15px;"></i>
                                        <p>No budgets found.</p>
                                        <p class="text-muted" style="font-size: 12px;">Create your first budget using the form on the right.</p>
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
                    <h3 class="box-title"><i class="fa fa-plus-circle"></i> Create Budget</h3>
                </div>
                <div class="box-body">
                    <form method="POST" action="<?php echo e(url('budgets')); ?>" id="createBudgetForm">
                        <?php echo csrf_field(); ?>
                        
                        <fieldset>
                            <legend>
                                <span class="form-section-icon"><i class="fa fa-info-circle"></i></span>
                                Budget Details
                            </legend>

                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'division_id')); ?>">
                                <label for="division_id">
                                    <i class="fa fa-sitemap"></i> Division <span class="text-danger">*</span>
                                </label>
                                <select class="form-control division_id" name="division_id" id="division_id" required>
                                    <option value="">-- Select Division --</option>
                                    <?php $__currentLoopData = $divisions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $division): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($division->id); ?>" <?php echo e(old('division_id') == $division->id ? 'selected' : ''); ?>>
                                            <?php echo e($division->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="help-text">Select the division for this budget</small>
                                <?php echo e(hasErrorForField($errors, 'division_id')); ?>

                            </div>

                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'year')); ?>">
                                <label for="year">
                                    <i class="fa fa-calendar"></i> Fiscal Year <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="year" 
                                       id="year" 
                                       class="form-control" 
                                       value="<?php echo e(old('year', date('Y'))); ?>"
                                       min="2020"
                                       max="2099"
                                       placeholder="e.g., <?php echo e(date('Y')); ?>"
                                       required>
                                <small class="help-text">Enter the fiscal year (e.g., <?php echo e(date('Y')); ?>)</small>
                                <?php echo e(hasErrorForField($errors, 'year')); ?>

                            </div>

                            <div class="form-group <?php echo e(hasErrorForClass($errors, 'total')); ?>">
                                <label for="total">
                                    <i class="fa fa-money-bill-wave"></i> Budget Amount <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-addon">R</div>
                                    <input type="number" 
                                           name="total" 
                                           id="total" 
                                           class="form-control" 
                                           value="<?php echo e(old('total')); ?>"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00"
                                           required>
                                </div>
                                <small class="help-text">Enter the total budget amount in Rands</small>
                                <?php echo e(hasErrorForField($errors, 'total')); ?>

                            </div>
                        </fieldset>

                        <div class="form-group" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-success btn-block btn-gradient">
                                <i class="fa fa-plus-circle"></i> Create Budget
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-lightbulb"></i> Budget Guidelines</h3>
                </div>
                <div class="box-body">
                    <p style="font-size: 13px; margin-bottom: 10px;"><strong>Best Practices:</strong></p>
                    <ul style="margin-left: 20px; font-size: 12px;">
                        <li>Create one budget per division per fiscal year</li>
                        <li>Use actual fiscal year dates (e.g., <?php echo e(date('Y')); ?>)</li>
                        <li>Set realistic budget amounts based on historical data</li>
                        <li>Review and adjust budgets quarterly</li>
                    </ul>
                    <hr>
                    <p style="font-size: 13px; margin-bottom: 10px;"><strong>Budget Planning Tips:</strong></p>
                    <ul style="margin-left: 20px; font-size: 12px;">
                        <li><strong>Asset Replacement:</strong> Plan for hardware lifecycle</li>
                        <li><strong>Software Licenses:</strong> Include annual subscriptions</li>
                        <li><strong>Maintenance:</strong> Budget for repairs and upgrades</li>
                        <li><strong>Contingency:</strong> Reserve 10-15% for emergencies</li>
                    </ul>
                </div>
            </div>

            
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-chart-bar"></i> Budget Statistics</h3>
                </div>
                <div class="box-body">
                    <?php
                        $totalBudget = $budgets->sum('total');
                        $currentYearBudgets = $budgets->where('year', date('Y'));
                        $currentYearTotal = $currentYearBudgets->sum('total');
                    ?>

                    <div class="info-box bg-aqua" style="min-height: 80px; margin-bottom: 15px;">
                        <span class="info-box-icon"><i class="fa fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total All Budgets</span>
                            <span class="info-box-number">R <?php echo e(number_format($totalBudget, 2)); ?></span>
                            <span class="progress-description">
                                All fiscal years
                            </span>
                        </div>
                    </div>

                    <div class="info-box bg-green" style="min-height: 80px; margin-bottom: 0;">
                        <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?php echo e(date('Y')); ?> Budgets</span>
                            <span class="info-box-number">R <?php echo e(number_format($currentYearTotal, 2)); ?></span>
                            <span class="progress-description">
                                <?php echo e($currentYearBudgets->count()); ?> division(s)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $(".division_id").select2({
            placeholder: "-- Select Division --",
            allowClear: true
        });

        // Initialize DataTable
        $('#table').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[1, "desc"]], // Sort by year descending
            columnDefs: [
                { orderable: false, targets: 3 } // Actions column
            ],
            language: {
                search: "Search budgets:",
                lengthMenu: "Show _MENU_ budgets per page",
                info: "Showing _START_ to _END_ of _TOTAL_ budgets",
                infoEmpty: "No budgets available",
                infoFiltered: "(filtered from _MAX_ total budgets)",
                zeroRecords: "No matching budgets found",
                emptyTable: "No budgets available"
            },
            dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>' +
                 '<"row"<"col-sm-12"<"table-responsive"tr>>>' +
                 '<"row"<"col-sm-5"i><"col-sm-7"p>>',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-csv"></i> CSV',
                    className: 'btn btn-info btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'copy',
                    text: '<i class="fa fa-copy"></i> Copy',
                    className: 'btn btn-default btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                }
            ]
        });

        // Form validation
        $('#createBudgetForm').on('submit', function(e) {
            var division = $('#division_id').val();
            var year = $('#year').val();
            var total = $('#total').val();

            if (!division || !year || !total) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }

            if (parseFloat(total) <= 0) {
                e.preventDefault();
                alert('Budget amount must be greater than 0.');
                return false;
            }
        });

        // Auto-dismiss alerts
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/budgets/index.blade.php ENDPATH**/ ?>