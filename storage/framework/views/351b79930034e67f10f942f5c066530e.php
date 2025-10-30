

<?php $__env->startSection('main-content'); ?>



<?php echo $__env->make('components.page-header', [
    'title' => 'Create New Ticket',
    'subtitle' => 'Submit a new support ticket',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Tickets', 'url' => route('tickets.index')],
        ['label' => 'Create']
    ]
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Information</h3>
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

          <form method="POST" action="<?php echo e(url('tickets')); ?>" id="ticket-create-form">
            <?php echo e(csrf_field()); ?>

            
            
            <input type="hidden" name="user_id" value="<?php echo e(old('user_id', Auth::id())); ?>">

            
            <fieldset>
              <legend><i class="fa fa-info-circle"></i> Basic Information</legend>

              <div class="form-group">
                <label>Creator / Reporter</label>
                <p class="form-control-static"><i class="fa fa-user"></i> <strong><?php echo e(Auth::user()->name); ?></strong> (<?php echo e(Auth::user()->email); ?>)</p>
                <small class="text-muted">You are creating this ticket on behalf of yourself</small>
              </div>

              <div class="form-group">
                <label for="subject">Subject <span class="text-red">*</span></label>
                <input type="text" class="form-control <?php $__errorArgs = ['subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="subject" id="subject" value="<?php echo e(old('subject')); ?>" required maxlength="255">
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
unset($__errorArgs, $__bag); ?>" rows="5" name="description" id="description" required minlength="10"><?php echo e(old('description')); ?></textarea>
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
                <select class="form-control ticket_type_id <?php $__errorArgs = ['ticket_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="ticket_type_id" id="ticket_type_id" required>
                  <option value="">-- Select Ticket Type --</option>
                  <?php $__currentLoopData = $ticketsTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketType->id); ?>" <?php echo e(old('ticket_type_id') == $ticketType->id ? 'selected' : ''); ?>><?php echo e($ticketType->type); ?></option>
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
                <select class="form-control ticket_priority_id <?php $__errorArgs = ['ticket_priority_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="ticket_priority_id" id="ticket_priority_id" required>
                  <option value="">-- Select Priority --</option>
                  <?php $__currentLoopData = $ticketsPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketsPriority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketsPriority->id); ?>" <?php echo e(old('ticket_priority_id') == $ticketsPriority->id ? 'selected' : ''); ?>><?php echo e($ticketsPriority->priority); ?></option>
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
                <label for="ticket_status_id">Initial Status</label>
                <select class="form-control ticket_status_id <?php $__errorArgs = ['ticket_status_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="ticket_status_id" id="ticket_status_id">
                  <option value="">-- Select Status (defaults to Open) --</option>
                  <?php $__currentLoopData = $ticketsStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketStatus->id); ?>" <?php echo e(old('ticket_status_id') == $ticketStatus->id ? 'selected' : ''); ?>><?php echo e($ticketStatus->status); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <small class="text-muted">Leave blank to set as "Open" automatically</small>
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
              <legend><i class="fa fa-map-marker"></i> Location & Assignment</legend>

              <div class="form-group">
                <label for="location_id">Location <span class="text-red">*</span></label>
                <select class="form-control location_id <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="location_id" id="location_id" required>
                  <option value="">-- Select Location --</option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>" <?php echo e(old('location_id') == $location->id ? 'selected' : ''); ?>><?php echo e($location->location_name); ?> - <?php echo e($location->building); ?>, <?php echo e($location->office); ?></option>
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
                <label for="asset_ids">Related Assets (Optional)</label>
                <select class="form-control asset_ids <?php $__errorArgs = ['asset_ids'];
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
unset($__errorArgs, $__bag); ?>" name="asset_ids[]" id="asset_ids" multiple>
                  <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($asset->id); ?>" <?php echo e((old('asset_ids') && in_array($asset->id, old('asset_ids'))) || (isset($preselectedAssetId) && $preselectedAssetId == $asset->id) ? 'selected' : ''); ?>>
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
                <i class="fa fa-save"></i> <b>Create Ticket</b>
              </button>
              <a href="<?php echo e(route('tickets.index')); ?>" class="btn btn-default btn-lg">
                <i class="fa fa-times"></i> Cancel
              </a>
            </div>
          </form>
        </div>
      </div>

      
      <?php if(count($errors)): ?>
        <div class="alert alert-danger">
          <h4><i class="icon fa fa-ban"></i> Validation Errors!</h4>
          <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
    
    
    <div class="col-md-4">
      
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-magic"></i> Quick Templates</h3>
        </div>
        <div class="box-body">
          <p class="text-muted"><small>Use pre-defined templates to speed up ticket creation</small></p>
          <form method="POST" action="/canned">
            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label for="canned_subject">Template</label>
              <select class="form-control subject" name="subject" id="canned_subject">
                <option value="">-- Select Template --</option>
                <?php $__currentLoopData = $ticketsCannedFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketsCannedField): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($ticketsCannedField->id); ?>"><?php echo e($ticketsCannedField->subject); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-info btn-block">
                <i class="fa fa-magic"></i> Use Template
              </button>
            </div>
          </form>
        </div>
      </div>

      
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-question-circle"></i> Help & Tips</h3>
        </div>
        <div class="box-body">
          <p><strong>Priority Guidelines:</strong></p>
          <ul class="list-unstyled">
            <li><span class="badge bg-red">High</span> System down, critical issue</li>
            <li><span class="badge bg-yellow">Medium</span> Affecting work but not critical</li>
            <li><span class="badge bg-green">Low</span> Minor issue or request</li>
          </ul>
          
          <hr>
          
          <p><strong>Common Ticket Types:</strong></p>
          <ul style="font-size: 12px;">
            <li><i class="fa fa-wrench"></i> Hardware Issue</li>
            <li><i class="fa fa-code"></i> Software Support</li>
            <li><i class="fa fa-wifi"></i> Network Problem</li>
            <li><i class="fa fa-user-plus"></i> Access Request</li>
          </ul>

          <hr>

          <p><strong>Tips for Better Support:</strong></p>
          <ul style="font-size: 12px;">
            <li>Be specific in your description</li>
            <li>Include error messages if any</li>
            <li>Mention when the issue started</li>
            <li>Select the correct asset</li>
          </ul>
        </div>
      </div>

      
      <div class="box box-warning">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-clock-o"></i> SLA Information</h3>
        </div>
        <div class="box-body">
          <p class="text-muted"><small>Expected response times based on priority:</small></p>
          <table class="table table-condensed" style="font-size: 12px;">
            <tr>
              <td><span class="badge bg-red">High</span></td>
              <td>4 hours</td>
            </tr>
            <tr>
              <td><span class="badge bg-yellow">Medium</span></td>
              <td>24 hours</td>
            </tr>
            <tr>
              <td><span class="badge bg-green">Low</span></td>
              <td>48 hours</td>
            </tr>
          </table>
          <p class="text-muted"><small><em>* SLA clock starts when ticket is created</em></small></p>
        </div>
      </div>
    </div>
  </div>

<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize Select2 for all dropdowns
      $(".location_id").select2({ placeholder: 'Select location', allowClear: true });
      $(".ticket_status_id").select2({ placeholder: 'Select status (optional)', allowClear: true });
      $(".ticket_type_id").select2({ placeholder: 'Select ticket type', allowClear: false });
      $(".ticket_priority_id").select2({ placeholder: 'Select priority', allowClear: false });
      $(".subject").select2({ placeholder: 'Select template', allowClear: true });
      $(".asset_ids").select2({ 
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

      // Add loading overlay on form submit
      $('#ticket-create-form').on('submit', function() {
        showLoading('Creating ticket...');
      });

      // Prevent enter key from submitting form
      $(":input").keypress(function(event){
        if (event.which == '10' || event.which == '13') {
          event.preventDefault();
        }
      });
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/tickets/create.blade.php ENDPATH**/ ?>