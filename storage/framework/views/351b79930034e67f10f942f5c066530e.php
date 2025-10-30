

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
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('tickets')); ?>" id="ticket-create-form">
            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label>User/Creator</label>
              <p class="form-control-static"><?php echo e(Auth::user()->name); ?></p>
              <input type="hidden" name="user_id" value="<?php echo e(old('user_id', Auth::id())); ?>">
            </div>
            <div class="form-group">
              <label for="location_id">Location</label>
              <select class="form-control location_id" name="location_id">
                <option value = ""></option>
                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->location_name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="asset_ids">Asset(s) (Optional)</label>
              <select class="form-control asset_ids" name="asset_ids[]" multiple>
                <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($asset->id); ?>" <?php echo e((old('asset_ids') && in_array($asset->id, old('asset_ids'))) || (isset($preselectedAssetId) && $preselectedAssetId == $asset->id) ? 'selected' : ''); ?>>
                        <?php echo e($asset->model_name ? $asset->model_name : 'Unknown Model'); ?> (<?php echo e($asset->asset_tag); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="ticket_status_id">Status</label>
              <select class="form-control ticket_status_id" name="ticket_status_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketStatus->id); ?>"><?php echo e($ticketStatus->status); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="ticket_type_id">Type</label>
              <select class="form-control ticket_type_id" name="ticket_type_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketType->id); ?>"><?php echo e($ticketType->type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="ticket_priority_id">Priority</label>
              <select class="form-control ticket_priority_id" name="ticket_priority_id">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketsPriority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketsPriority->id); ?>"><?php echo e($ticketsPriority->priority); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" class="form-control" name="subject" value="<?php echo e(old('subject')); ?>">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" rows="5" name="description"><?php echo e(old('description')); ?></textarea>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><b>Add New Ticket</b></button>
            </div>
          </form>
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
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Canned Fields</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="/canned">
            <?php echo e(csrf_field()); ?>

            <div class="form-group">
              <label for="subject">Subject</label>
              <select class="form-control subject" name="subject">
                <option value = ""></option>
                <?php $__currentLoopData = $ticketsCannedFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticketsCannedField): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ticketsCannedField->id); ?>"><?php echo e($ticketsCannedField->subject); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary">Use Canned Fields</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(".location_id").select2();
      $(".ticket_status_id").select2();
      $(".ticket_type_id").select2();
      $(".ticket_priority_id").select2();
      $(".subject").select2();
        $(".asset_ids").select2({ placeholder: 'Select asset(s)', allowClear: true });

      // Add loading overlay on form submit
      $('#ticket-create-form').on('submit', function() {
        showLoading('Creating ticket...');
      });
    });
  </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/tickets/create.blade.php ENDPATH**/ ?>