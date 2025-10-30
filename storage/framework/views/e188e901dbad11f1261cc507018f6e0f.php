

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/admin/ticket-canned-fields/<?php echo e($ticketsCannedField->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'user_id')); ?>">
              <label for="user_id">Agent</label>
              <select class="form-control user_id" name="user_id">
                <option value = ""></option>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($user->id); ?>"
                    <?php if($user->id == $ticketsCannedField->user_id): ?>
                      selected
                    <?php endif; ?>
                  ><?php echo e($user->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'user_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'location_id')); ?>">
              <label for="location_id">Location</label>
              <select class="form-control location_id" name="location_id">
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($location->id); ?>"
                    <?php if($location->id == $ticketsCannedField->location_id): ?>
                      selected
                    <?php endif; ?>
                  ><?php echo e($location->location_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'location_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ticket_status_id')); ?>">
              <label for="ticket_status_id">Status</label>
              <select class="form-control ticket_status_id" name="ticket_status_id">
                <?php $__currentLoopData = $ticketsStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($ticketStatus->id); ?>"
                    <?php if($ticketStatus->id == $ticketsCannedField->ticket_status_id): ?>
                      selected
                    <?php endif; ?>
                  ><?php echo e($ticketStatus->status); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'ticket_status_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ticket_type_id')); ?>">
              <label for="ticket_type_id">Type</label>
              <select class="form-control ticket_type_id" name="ticket_type_id">
                <?php $__currentLoopData = $ticketsTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($ticketType->id); ?>"
                    <?php if($ticketType->id == $ticketsCannedField->ticket_type_id): ?>
                      selected
                    <?php endif; ?>
                  ><?php echo e($ticketType->type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'ticket_type_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'ticket_priority_id')); ?>">
              <label for="ticket_priority_id">Priority</label>
              <select class="form-control ticket_priority_id" name="ticket_priority_id">
                <?php $__currentLoopData = $ticketsPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketPriority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($ticketPriority->id); ?>"
                    <?php if($ticketPriority->id == $ticketsCannedField->ticket_priority_id): ?>
                      selected
                    <?php endif; ?>
                  ><?php echo e($ticketPriority->priority); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php echo e(hasErrorForField($errors, 'ticket_priority_id')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'subject')); ?>">
              <label for="subject">Subject</label>
              <input type="text" class="form-control" name="subject" value="<?php echo e($ticketsCannedField->subject); ?>">
              <?php echo e(hasErrorForField($errors, 'subject')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'description')); ?>">
              <label for="description">Description</label>
              <textarea class="form-control" rows="5" name="description"><?php echo e($ticketsCannedField->description); ?></textarea>
              <?php echo e(hasErrorForField($errors, 'description')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Edit Ticket Canned Fields</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".user_id").select2();
      $(".location_id").select2();
      $(".ticket_status_id").select2();
      $(".ticket_type_id").select2();
      $(".ticket_priority_id").select2();
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\admin\ticket-canned-fields\edit.blade.php ENDPATH**/ ?>