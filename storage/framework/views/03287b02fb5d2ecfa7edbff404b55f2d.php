

<?php $__env->startSection('main-content'); ?>



<?php echo $__env->make('components.page-header', [
    'title' => 'Edit Ticket #' . $ticket->ticket_code,
    'subtitle' => 'Update ticket details',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Tickets', 'url' => route('tickets.index')],
        ['label' => 'Edit #' . $ticket->ticket_code]
    ],
    'actions' => '<a href="'.route('tickets.show', $ticket).'" class="btn btn-default">
        <i class="fa fa-eye"></i> View Ticket
    </a>
    <a href="'.route('tickets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Edit Ticket #<?php echo e($ticket->ticket_code); ?></h3>
            </div>
            <div class="box-body">
                
                <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fa fa-check"></i> <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fa fa-ban"></i> <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> <strong>Ticket Info:</strong>
                    Created: <?php echo e($ticket->created_at ? $ticket->created_at->format('d M Y H:i') : 'N/A'); ?> by <?php echo e($ticket->user->name ?? 'N/A'); ?> |
                    Last Updated: <?php echo e($ticket->updated_at ? $ticket->updated_at->format('d M Y H:i') : 'N/A'); ?>

                    <?php if($ticket->resolved_at): ?>
                        | <span class="text-success"><i class="fa fa-check-circle"></i> Resolved: <?php echo e($ticket->resolved_at->format('d M Y H:i')); ?></span>
                    <?php endif; ?>
                </div>

                <form method="POST" action="<?php echo e(route('tickets.update', $ticket)); ?>" id="ticket-edit-form">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    
                    <fieldset>
                        <legend><i class="fa fa-info-circle"></i> Basic Information</legend>

                        <div class="form-group">
                            <label for="subject">Subject <span class="text-red">*</span></label>
                            <input type="text" 
                                   class="form-control <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   name="subject" 
                                   id="subject"
                                   value="<?php echo e(old('subject', $ticket->subject)); ?>" 
                                   required maxlength="255">
                            <small class="text-muted">Brief summary of the issue (max 255 characters)</small>
                            <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span class="text-red">*</span></label>
                            <span id="char-counter">0 / 10 characters (minimum 10)</span>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      name="description" 
                                      id="description" 
                                      rows="5" 
                                      required minlength="10"><?php echo e(old('description', $ticket->description)); ?></textarea>
                            <small class="text-muted">Detailed description of the issue or request (minimum 10 characters)</small>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="ticket_type_id">Ticket Type <span class="text-red">*</span></label>
                            <select class="form-control <?php $__errorArgs = ['ticket_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    name="ticket_type_id" 
                                    id="ticket_type_id" 
                                    required>
                                <option value="">-- Select Ticket Type --</option>
                                <?php $__currentLoopData = $ticketsTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>" 
                                            <?php echo e(old('ticket_type_id', $ticket->ticket_type_id) == $type->id ? 'selected' : ''); ?>>
                                        <?php echo e($type->type); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Category of request (e.g., Hardware Issue, Software Support, Network Problem)</small>
                            <?php $__errorArgs = ['ticket_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="ticket_priority_id">Priority <span class="text-red">*</span></label>
                            <select class="form-control <?php $__errorArgs = ['ticket_priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    name="ticket_priority_id" 
                                    id="ticket_priority_id" 
                                    required>
                                <option value="">-- Select Priority --</option>
                                <?php $__currentLoopData = $ticketsPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($priority->id); ?>" 
                                            <?php echo e(old('ticket_priority_id', $ticket->ticket_priority_id) == $priority->id ? 'selected' : ''); ?>>
                                        <?php echo e($priority->priority); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Urgency level - affects SLA due date (High = urgent, Medium = normal, Low = can wait)</small>
                            <?php $__errorArgs = ['ticket_priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="ticket_status_id">Status <span class="text-red">*</span></label>
                            <select class="form-control <?php $__errorArgs = ['ticket_status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    name="ticket_status_id" 
                                    id="ticket_status_id" 
                                    required>
                                <option value="">-- Select Status --</option>
                                <?php $__currentLoopData = $ticketsStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($status->id); ?>" 
                                            <?php echo e(old('ticket_status_id', $ticket->ticket_status_id) == $status->id ? 'selected' : ''); ?>>
                                        <?php echo e($status->status); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Current ticket status</small>
                            <?php $__errorArgs = ['ticket_status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </fieldset>

                    
                    <fieldset>
                        <legend><i class="fa fa-user"></i> Assignment & Location</legend>

                        <div class="form-group">
                            <label for="assigned_to">Assigned To</label>
                            <select class="form-control <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    name="assigned_to" 
                                    id="assigned_to">
                                <option value="">-- Unassigned --</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" 
                                            <?php echo e(old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : ''); ?>>
                                        <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Technician responsible for resolving this ticket</small>
                            <?php $__errorArgs = ['assigned_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="location_id">Location <span class="text-red">*</span></label>
                            <select class="form-control <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    name="location_id" 
                                    id="location_id" required>
                                <option value="">-- Select Location --</option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($location->id); ?>" 
                                            <?php echo e(old('location_id', $ticket->location_id) == $location->id ? 'selected' : ''); ?>>
                                        <?php echo e($location->location_name); ?> - <?php echo e($location->building); ?>, <?php echo e($location->office); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Physical location where issue is occurring</small>
                            <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </fieldset>

                    
                    <fieldset>
                        <legend><i class="fa fa-laptop"></i> Asset Association</legend>

                        <div class="form-group">
                            <label for="asset_id">Related Assets (Optional)</label>
                            <select class="form-control <?php $__errorArgs = ['asset_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php $__errorArgs = ['asset_ids.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    name="asset_ids[]" 
                                    id="asset_id" multiple>
                                <?php $selectedAssets = old('asset_ids', $ticket->assets->pluck('id')->toArray()); ?>
                                <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($asset->id); ?>" 
                                            <?php echo e(in_array($asset->id, $selectedAssets ?? []) ? 'selected' : ''); ?>>
                                        <?php echo e($asset->model_name ? $asset->model_name : 'Unknown Model'); ?> (<?php echo e($asset->asset_tag); ?>) - <?php echo e($asset->location ? $asset->location->location_name : 'No Location'); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Select one or more assets related to this ticket (use Ctrl/Cmd + Click for multiple)</small>
                            <?php $__errorArgs = ['asset_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <?php $__errorArgs = ['asset_ids.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </fieldset>

                    
                    <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-save"></i> <b>Update Ticket</b>
                        </button>
                        <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="btn btn-info btn-lg">
                            <i class="fa fa-eye"></i> View
                        </a>
                        <a href="<?php echo e(route('tickets.index')); ?>" class="btn btn-default btn-lg">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
    <div class="col-md-4">
        
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-ticket"></i> Ticket Details</h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal" style="margin-bottom: 0;">
                    <dt>Ticket Code:</dt>
                    <dd><strong class="text-primary"><?php echo e($ticket->ticket_code); ?></strong></dd>
                    
                    <dt>Created By:</dt>
                    <dd><?php echo e($ticket->user->name ?? 'N/A'); ?></dd>
                    
                    <dt>Created At:</dt>
                    <dd><?php echo e($ticket->created_at->format('M j, Y H:i')); ?></dd>
                    
                    <dt>Last Updated:</dt>
                    <dd><?php echo e($ticket->updated_at->format('M j, Y H:i')); ?></dd>
                    
                    <?php if($ticket->resolved_at): ?>
                        <dt>Resolved At:</dt>
                        <dd><span class="text-success"><i class="fa fa-check-circle"></i> <?php echo e($ticket->resolved_at->format('M j, Y H:i')); ?></span></dd>
                    <?php endif; ?>

                    <?php if($ticket->sla_due): ?>
                        <dt>SLA Due:</dt>
                        <dd>
                            <?php
                                $now = now();
                                $isOverdue = $now->gt($ticket->sla_due);
                                $hoursRemaining = $now->diffInHours($ticket->sla_due, false);
                            ?>
                            <?php if($isOverdue): ?>
                                <span class="text-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo e($ticket->sla_due->format('M j, Y H:i')); ?></span>
                                <br><small class="text-danger">Overdue by <?php echo e(abs($hoursRemaining)); ?> hours</small>
                            <?php elseif($hoursRemaining < 4): ?>
                                <span class="text-warning"><i class="fa fa-clock-o"></i> <?php echo e($ticket->sla_due->format('M j, Y H:i')); ?></span>
                                <br><small class="text-warning"><?php echo e($hoursRemaining); ?> hours remaining</small>
                            <?php else: ?>
                                <span class="text-success"><i class="fa fa-check"></i> <?php echo e($ticket->sla_due->format('M j, Y H:i')); ?></span>
                                <br><small class="text-success"><?php echo e($hoursRemaining); ?> hours remaining</small>
                            <?php endif; ?>
                        </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        
        <?php if($ticket->assets->count() > 0): ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-laptop"></i> Current Assets</h3>
                </div>
                <div class="box-body">
                    <ul class="list-unstyled" style="font-size: 12px; margin-bottom: 0;">
                        <?php $__currentLoopData = $ticket->assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li style="margin-bottom: 8px;">
                                <i class="fa fa-check-circle text-success"></i>
                                <strong><?php echo e($asset->asset_tag); ?></strong><br>
                                <span class="text-muted"><?php echo e($asset->model_name ?? 'Unknown Model'); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-question-circle"></i> Edit Tips</h3>
            </div>
            <div class="box-body">
                <p><strong>Priority Guidelines:</strong></p>
                <ul class="list-unstyled">
                    <li><span class="badge bg-red">High</span> System down, critical issue</li>
                    <li><span class="badge bg-yellow">Medium</span> Affecting work but not critical</li>
                    <li><span class="badge bg-green">Low</span> Minor issue or request</li>
                </ul>
                
                <hr>
                
                <p><strong>Status Options:</strong></p>
                <ul style="font-size: 12px;">
                    <li><i class="fa fa-circle-o"></i> Open - Just created</li>
                    <li><i class="fa fa-cog"></i> In Progress - Being worked on</li>
                    <li><i class="fa fa-pause"></i> On Hold - Waiting for info</li>
                    <li><i class="fa fa-check"></i> Resolved - Issue fixed</li>
                    <li><i class="fa fa-times"></i> Closed - Completed</li>
                </ul>
            </div>
        </div>

        
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="box-body">
                <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="btn btn-info btn-block btn-sm">
                    <i class="fa fa-eye"></i> View Full Ticket
                </a>
                <a href="<?php echo e(route('tickets.index')); ?>" class="btn btn-default btn-block btn-sm">
                    <i class="fa fa-list"></i> Back to All Tickets
                </a>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Initialize Select2 for all dropdowns
    $('#ticket_type_id').select2({ placeholder: 'Select ticket type', allowClear: false });
    $('#ticket_priority_id').select2({ placeholder: 'Select priority', allowClear: false });
    $('#ticket_status_id').select2({ placeholder: 'Select status', allowClear: false });
    $('#assigned_to').select2({ placeholder: 'Select technician (optional)', allowClear: true });
    $('#location_id').select2({ placeholder: 'Select location', allowClear: false });
    
    // Init multi-select for assets with better styling
    $('#asset_id').select2({ 
        placeholder: 'Search and select asset(s)', 
        allowClear: true,
        width: '100%'
    });

    // Character counter for description
    function updateCharCounter() {
        var length = $('#description').val().length;
        var minLength = 10;
        var counter = $('#char-counter');
        
        counter.text(length + ' / ' + minLength + ' characters (minimum ' + minLength + ')');
        
        if (length >= minLength) {
            counter.removeClass('invalid').addClass('valid');
        } else {
            counter.removeClass('valid').addClass('invalid');
        }
    }

    // Update counter on load and on input
    updateCharCounter();
    $('#description').on('input', updateCharCounter);

    // Form submit with loading overlay
    $('#ticket-edit-form').on('submit', function() {
        showLoading('Updating ticket...');
    });

    // Prevent enter key from submitting form
    $(":input").keypress(function(event){
        if (event.which == '10' || event.which == '13') {
            event.preventDefault();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/tickets/edit.blade.php ENDPATH**/ ?>