

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', [
    'title' => 'Resolution History',
    'subtitle' => 'View all conflict resolutions and audit trail',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Master Data', 'url' => route('masterdata.index')],
        ['label' => 'Imports', 'url' => route('masterdata.imports')],
        ['label' => 'Conflicts', 'url' => route('imports.conflicts.index', $import->import_id)],
        ['label' => 'History']
    ],
    'actions' => '<a href="'.route('imports.conflicts.index', $import->import_id).'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to Conflicts
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-md-12">
        <!-- Summary Statistics -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-history"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Resolutions</span>
                        <span class="info-box-number"><?php echo e($resolutionHistory->count()); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Completed</span>
                        <span class="info-box-number"><?php echo e($resolutionHistory->where('choice', 'create_new')->count() + $resolutionHistory->where('choice', 'update_existing')->count()); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-orange"><i class="fa fa-forward"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Skipped</span>
                        <span class="info-box-number"><?php echo e($resolutionHistory->where('choice', 'skip')->count()); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-compress"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Merged</span>
                        <span class="info-box-number"><?php echo e($resolutionHistory->where('choice', 'merge')->count()); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolution Timeline -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-clock-o"></i> Resolution Timeline
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <?php if($resolutionHistory->count() > 0): ?>
                    <div class="timeline">
                        <?php $__empty_1 = true; $__currentLoopData = $resolutionHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $resolution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="time-label">
                                <span class="bg-red">
                                    <?php echo e($resolution->created_at->format('M d, Y')); ?>

                                </span>
                            </div>
                            <div>
                                <i class="fa fa-check-circle bg-green"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fa fa-clock-o"></i> 
                                        <?php echo e($resolution->created_at->format('h:i A')); ?>

                                    </span>
                                    <h3 class="timeline-header">
                                        <strong>Conflict #<?php echo e($resolution->conflict_id); ?></strong> - Row <?php echo e($resolution->conflict->row_number); ?>

                                    </h3>
                                    <div class="timeline-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <dl class="dl-horizontal">
                                                    <dt>Conflict Type:</dt>
                                                    <dd>
                                                        <span class="badge" style="background-color: <?php switch($resolution->conflict->conflict_type):
                                                            case ('duplicate_key'): ?> #DD4B39 <?php break; ?>
                                                            <?php case ('duplicate_record'): ?> #F39C12 <?php break; ?>
                                                            <?php case ('foreign_key_not_found'): ?> #3498DB <?php break; ?>
                                                            <?php case ('invalid_data'): ?> #9B59B6 <?php break; ?>
                                                            <?php case ('business_rule_violation'): ?> #E74C3C <?php break; ?>
                                                            <?php default: ?> #95A5A6
                                                        <?php endswitch; ?>">
                                                            <?php echo e($resolution->conflict->getConflictTypeLabel()); ?>

                                                        </span>
                                                    </dd>
                                                    
                                                    <dt>Resolution:</dt>
                                                    <dd>
                                                        <span class="label <?php switch($resolution->choice):
                                                            case ('skip'): ?> label-warning <?php break; ?>
                                                            <?php case ('create_new'): ?> label-success <?php break; ?>
                                                            <?php case ('update_existing'): ?> label-info <?php break; ?>
                                                            <?php case ('merge'): ?> label-primary <?php break; ?>
                                                            <?php default: ?> label-default
                                                        <?php endswitch; ?>">
                                                            <?php echo e($resolution->getChoiceLabel()); ?>

                                                        </span>
                                                    </dd>
                                                </dl>
                                            </div>
                                            <div class="col-md-6">
                                                <dl class="dl-horizontal">
                                                    <dt>Resolved By:</dt>
                                                    <dd>
                                                        <a href="#"><?php echo e($resolution->user->name); ?></a>
                                                        <br>
                                                        <small class="text-muted"><?php echo e($resolution->user->email); ?></small>
                                                    </dd>
                                                    
                                                    <dt>Timestamp:</dt>
                                                    <dd>
                                                        <?php echo e($resolution->created_at->format('M d, Y h:i A')); ?>

                                                        <br>
                                                        <small class="text-muted"><?php echo e($resolution->created_at->diffForHumans()); ?></small>
                                                    </dd>
                                                </dl>
                                            </div>
                                        </div>

                                        <?php if($resolution->choice_details): ?>
                                            <div class="row" style="margin-top: 10px;">
                                                <div class="col-md-12">
                                                    <strong>Additional Details:</strong>
                                                    <div class="well well-sm" style="margin: 5px 0;">
                                                        <pre><?php echo e(json_encode($resolution->choice_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No resolutions recorded yet
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No resolution history available for this import
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Users Summary -->
        <?php
            $resolutionsByUser = $resolutionHistory->groupBy('user_id');
        ?>
        <?php if($resolutionsByUser->count() > 0): ?>
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-users"></i> Resolutions by User
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <?php $__currentLoopData = $resolutionsByUser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $resolutions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $user = $resolutions->first()->user;
                            ?>
                            <div class="col-md-6">
                                <div class="box-group" id="accordion">
                                    <div class="panel box box-primary">
                                        <div class="box-header with-border" data-toggle="collapse" data-parent="#accordion" href="#user-<?php echo e($userId); ?>">
                                            <h4 class="box-title">
                                                <i class="fa fa-user-circle"></i> <?php echo e($user->name); ?>

                                                <span class="badge bg-blue" style="margin-left: 10px;"><?php echo e($resolutions->count()); ?></span>
                                            </h4>
                                        </div>
                                        <div id="user-<?php echo e($userId); ?>" class="panel-collapse collapse">
                                            <div class="box-body">
                                                <ul class="list-unstyled">
                                                    <?php $__currentLoopData = $resolutions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resolution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li>
                                                            <strong>Conflict #<?php echo e($resolution->conflict_id); ?></strong> (Row <?php echo e($resolution->conflict->row_number); ?>)
                                                            <br>
                                                            <span class="label <?php switch($resolution->choice):
                                                                case ('skip'): ?> label-warning <?php break; ?>
                                                                <?php case ('create_new'): ?> label-success <?php break; ?>
                                                                <?php case ('update_existing'): ?> label-info <?php break; ?>
                                                                <?php case ('merge'): ?> label-primary <?php break; ?>
                                                                <?php default: ?> label-default
                                                            <?php endswitch; ?>">
                                                                <?php echo e($resolution->getChoiceLabel()); ?>

                                                            </span>
                                                            <br>
                                                            <small class="text-muted"><?php echo e($resolution->created_at->diffForHumans()); ?></small>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                                <?php if($resolutions->count() > 5): ?>
                                                    <p class="text-center text-muted" style="margin-top: 10px;">
                                                        <small>and <?php echo e($resolutions->count() - 5); ?> more...</small>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 40px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #ddd;
}

.time-label {
    position: relative;
    margin: 10px 0 20px 0;
}

.time-label > span {
    position: relative;
    display: inline-block;
    background: #fff;
    padding: 5px 10px;
    border-radius: 4px;
}

.timeline > div {
    margin-bottom: 20px;
    margin-left: 80px;
    position: relative;
}

.timeline > div > i {
    position: absolute;
    left: -64px;
    top: 0;
    font-size: 24px;
    width: 50px;
    height: 50px;
    line-height: 50px;
    text-align: center;
    color: #fff;
    border-radius: 50%;
    background: #ddd;
}

.timeline-item {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    padding: 0;
    background: #fff;
    border-radius: 3px;
}

.timeline-header {
    margin: 0;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.timeline-body {
    padding: 10px;
}

.time-label {
    margin-top: 20px;
    margin-bottom: 10px;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\imports\conflicts\history.blade.php ENDPATH**/ ?>