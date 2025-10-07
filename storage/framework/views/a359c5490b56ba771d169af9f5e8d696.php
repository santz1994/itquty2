

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
          <div class="box-body no-padding">
            <div class="mailbox-read-info">
              <h3><?php echo e($ticket->subject); ?></h3>
              <h5><?php echo e($ticket->user->name); ?>

                <?php $createdDate = \Carbon\Carbon::parse($ticket->created_at); ?>
                <span class="mailbox-read-time pull-right">Ticket logged on <?php echo e($createdDate->format('l, j F Y, H:i')); ?></span></h5>
            </div>
            <div class="mailbox-read-message">
              <?php echo nl2br($ticket->description); ?>

            </div>
            <!-- /.mailbox-read-message -->
            <hr>
          <ul class="timeline">
            <?php $__currentLoopData = $ticketEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketEntry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php $createdDate = \Carbon\Carbon::parse($ticketEntry->created_at); ?>
					    <!-- timeline item -->
					    <li>
				        <!-- timeline icon -->
				        <i class="fa fa-user bg-blue"></i>
				        <div class="timeline-item">
			            <span class="time"><?php echo e($createdDate->format('l, j F Y, H:i')); ?></span>

			            <h3 class="timeline-header"><?php echo e($ticketEntry->user->name); ?></h3>

			            <div class="timeline-body">
										<dl class="dl-horizontal">
				              <dt>Note:</dt><dd><?php echo e($ticketEntry->note); ?></dd>
										</dl>
			            </div>
			            <div class="timeline-footer">
			            </div>
				        </div>
				    	</li>
					    <!-- END timeline item -->
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</ul>
          <div class="box-footer">
            <button type="button" class="btn btn-default" id='note'><i class="fa fa-pencil"></i> Add Note</button>
            <div id='new-note' style='display: none'>
              <form method="POST" action="/tickets/<?php echo e($ticket->id); ?>">
                <?php echo e(csrf_field()); ?>

                <div class="form-group">
                  <label for="note">New Note</label>
                  <textarea name="note" class="form-control" rows="5"><?php echo e(old('note')); ?></textarea>
                </div>

                <div class="form-group">
                  <button type="submit" class="btn btn-primary"><b>Add New Note</b></button>
                </div>
              </form>
            </div>
          </div>
          <div class="text-center"><a class="btn btn-primary" href="<?php echo e(URL::previous()); ?>">Back</a></div><br>
        </div>
      </div>
      <?php if(count($errors)): ?>
        <ul>
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      <?php endif; ?>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Ticket Details</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/tickets/<?php echo e($ticket->id); ?>">
            <?php echo e(method_field('PATCH')); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label for="user_id">Agent</label>
              <select class="form-control user_id" name="user_id">
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($ticket->user_id == $user->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="location_id">Location</label>
              <select class="form-control location_id" name="location_id">
                <option value = ""></option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($ticket->location_id == $location->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($location->id); ?>"><?php echo e($location->location_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="ticket_status_id">Status</label>
              <select class="form-control ticket_status_id" name="ticket_status_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($ticket->ticket_status_id == $ticketStatus->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($ticketStatus->id); ?>"><?php echo e($ticketStatus->status); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="ticket_type_id">Type</label>
              <select class="form-control ticket_type_id" name="ticket_type_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($ticket->ticket_type_id == $ticketType->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($ticketType->id); ?>"><?php echo e($ticketType->type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="ticket_priority_id">Priority</label>
              <select class="form-control ticket_priority_id" name="ticket_priority_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketPriority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option
                    <?php if($ticket->ticket_priority_id == $ticketPriority->id): ?>
                      selected
                    <?php endif; ?>
                  value="<?php echo e($ticketPriority->id); ?>"><?php echo e($ticketPriority->priority); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Update Ticket</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    $("#note").click(function() {
      $("#new-note").toggle('1500');
    });
  </script>
  <?php if(Session::has('status')): ?>
    <script>
      $(document).ready(function() {
        Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/tickets/show.blade.php ENDPATH**/ ?>