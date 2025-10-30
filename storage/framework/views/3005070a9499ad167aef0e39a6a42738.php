

<?php $__env->startSection('main-content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-history"></i> Audit Log Details #<?php echo e($auditLog->id); ?>

                    </h3>
                    <div class="box-tools pull-right">
                        <a href="<?php echo e(route('audit-logs.index')); ?>" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h4 class="text-primary"><i class="fa fa-info-circle"></i> Basic Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">ID</th>
                                    <td><?php echo e($auditLog->id); ?></td>
                                </tr>
                                <tr>
                                    <th>Date/Time</th>
                                    <td>
                                        <?php echo e($auditLog->created_at->format('Y-m-d H:i:s')); ?>

                                        <br>
                                        <small class="text-muted">(<?php echo e($auditLog->created_at->diffForHumans()); ?>)</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>User</th>
                                    <td>
                                        <?php if($auditLog->user): ?>
                                            <strong><?php echo e($auditLog->user->name); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($auditLog->user->email); ?></small>
                                            <br>
                                            <a href="<?php echo e(route('audit-logs.index', ['user_id' => $auditLog->user_id])); ?>" class="btn btn-xs btn-info">
                                                <i class="fa fa-search"></i> View all logs by this user
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Action</th>
                                    <td>
                                        <?php
                                            $actionBadgeClass = [
                                                'create' => 'success',
                                                'update' => 'info',
                                                'delete' => 'danger',
                                                'login' => 'primary',
                                                'logout' => 'default',
                                                'failed_login' => 'warning',
                                            ][$auditLog->action] ?? 'default';
                                        ?>
                                        <span class="label label-<?php echo e($actionBadgeClass); ?>" style="font-size: 14px;">
                                            <?php echo e($auditLog->action_name); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Event Type</th>
                                    <td>
                                        <?php
                                            $eventTypeBadgeClass = [
                                                'model' => 'primary',
                                                'auth' => 'success',
                                                'system' => 'warning',
                                            ][$auditLog->event_type] ?? 'default';
                                        ?>
                                        <span class="label label-<?php echo e($eventTypeBadgeClass); ?>" style="font-size: 14px;">
                                            <?php echo e(ucfirst($auditLog->event_type)); ?>

                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Model & Request Information -->
                        <div class="col-md-6">
                            <h4 class="text-primary"><i class="fa fa-database"></i> Model & Request Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Model Type</th>
                                    <td>
                                        <?php if($auditLog->model_type): ?>
                                            <span class="badge bg-purple"><?php echo e($auditLog->model_name); ?></span>
                                            <br>
                                            <small class="text-muted"><?php echo e($auditLog->model_type); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Model ID</th>
                                    <td>
                                        <?php if($auditLog->model_id): ?>
                                            <strong>#<?php echo e($auditLog->model_id); ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>IP Address</th>
                                    <td>
                                        <code><?php echo e($auditLog->ip_address ?? 'N/A'); ?></code>
                                    </td>
                                </tr>
                                <tr>
                                    <th>User Agent</th>
                                    <td>
                                        <small><?php echo e($auditLog->user_agent ?? 'N/A'); ?></small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <h4 class="text-primary"><i class="fa fa-align-left"></i> Description</h4>
                            <div class="well">
                                <?php echo e($auditLog->description ?? 'No description available'); ?>

                            </div>
                        </div>
                    </div>

                    <!-- Changes (Old vs New Values) -->
                    <?php if($auditLog->old_values || $auditLog->new_values): ?>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <h4 class="text-primary"><i class="fa fa-exchange"></i> Changes</h4>
                                
                                <?php
                                    $changes = $auditLog->changes;
                                ?>

                                <?php if(!empty($changes)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="25%">Field</th>
                                                    <th width="37.5%">Old Value</th>
                                                    <th width="37.5%">New Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $values): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><strong><?php echo e(ucfirst(str_replace('_', ' ', $field))); ?></strong></td>
                                                        <td>
                                                            <span class="text-danger">
                                                                <?php if(is_null($values['old'])): ?>
                                                                    <em class="text-muted">null</em>
                                                                <?php elseif(is_bool($values['old'])): ?>
                                                                    <?php echo e($values['old'] ? 'true' : 'false'); ?>

                                                                <?php elseif(is_array($values['old'])): ?>
                                                                    <pre><?php echo e(json_encode($values['old'], JSON_PRETTY_PRINT)); ?></pre>
                                                                <?php else: ?>
                                                                    <?php echo e($values['old']); ?>

                                                                <?php endif; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-success">
                                                                <?php if(is_null($values['new'])): ?>
                                                                    <em class="text-muted">null</em>
                                                                <?php elseif(is_bool($values['new'])): ?>
                                                                    <?php echo e($values['new'] ? 'true' : 'false'); ?>

                                                                <?php elseif(is_array($values['new'])): ?>
                                                                    <pre><?php echo e(json_encode($values['new'], JSON_PRETTY_PRINT)); ?></pre>
                                                                <?php else: ?>
                                                                    <?php echo e($values['new']); ?>

                                                                <?php endif; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <!-- Show raw JSON if changes can't be parsed -->
                                    <div class="row">
                                        <?php if($auditLog->old_values): ?>
                                            <div class="col-md-6">
                                                <h5>Old Values:</h5>
                                                <pre><?php echo e(json_encode(json_decode($auditLog->old_values, true), JSON_PRETTY_PRINT)); ?></pre>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($auditLog->new_values): ?>
                                            <div class="col-md-6">
                                                <h5>New Values:</h5>
                                                <pre><?php echo e(json_encode(json_decode($auditLog->new_values, true), JSON_PRETTY_PRINT)); ?></pre>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Related Logs -->
                    <?php if($auditLog->model_type && $auditLog->model_id): ?>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <h4 class="text-primary"><i class="fa fa-link"></i> Related Logs</h4>
                                <a href="<?php echo e(route('audit-logs.index', ['model_type' => class_basename($auditLog->model_type), 'model_id' => $auditLog->model_id])); ?>" 
                                   class="btn btn-info">
                                    <i class="fa fa-search"></i> View all logs for this <?php echo e($auditLog->model_name); ?> #<?php echo e($auditLog->model_id); ?>

                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="box-footer">
                    <a href="<?php echo e(route('audit-logs.index')); ?>" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\audit_logs\show.blade.php ENDPATH**/ ?>