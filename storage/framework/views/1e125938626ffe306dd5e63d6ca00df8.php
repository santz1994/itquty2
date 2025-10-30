

<?php $__env->startSection('main-content'); ?>

<?php echo $__env->make('components.page-header', [
    'title' => 'Ticket #' . $ticket->ticket_code,
    'subtitle' => $ticket->subject,
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Tickets', 'url' => route('tickets.index')],
        ['label' => '#' . $ticket->ticket_code]
    ],
    'actions' => '<a href="'.route('tickets.edit', $ticket).'" class="btn btn-primary">
        <i class="fa fa-edit"></i> Edit Ticket
    </a>
    <a href="'.route('tickets.print', $ticket).'" class="btn btn-default" target="_blank">
        <i class="fa fa-print"></i> Print
    </a>
    <a href="'.route('tickets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
          <div class="box-body no-padding">
            <div class="mailbox-read-info">
              <h3><?php echo e($ticket->subject); ?></h3>
              <h5><?php echo e($ticket->user->name); ?>

                <?php $createdDate = \Carbon\Carbon::parse($ticket->created_at); ?>
                <span class="mailbox-read-time pull-right">Ticket logged on <?php echo e($createdDate->format('l, j F Y, H:i')); ?></span></h5>
            </div>
            <div class="mailbox-read-message">
              <?php echo nl2br(e($ticket->description)); ?>

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
            <?php if((auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('admin')) || $ticket->assigned_to == auth()->id()): ?>
              <a href="<?php echo e(route('tickets.edit', $ticket)); ?>" class="btn btn-primary">
                <i class="fa fa-edit"></i> Edit Ticket
              </a>
            <?php endif; ?>
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
      
      <!-- File Attachments Section -->
      <div class="box box-default">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-paperclip"></i> Attachments</h3>
        </div>
        <div class="box-body">
          <?php echo $__env->make('partials.file-uploader', [
            'model_type' => 'ticket',
            'model_id' => $ticket->id,
            'collection' => 'attachments'
          ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\tickets\show.blade.php ENDPATH**/ ?>