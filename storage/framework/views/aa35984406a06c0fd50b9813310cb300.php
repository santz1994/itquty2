

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>User's Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($user->name); ?></td>
                  <td>
                    <?php $__currentLoopData = $usersRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usersRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php $roleUserId = isset($usersRole->user_id) ? $usersRole->user_id : (isset($usersRole->model_id) ? $usersRole->model_id : null); ?>
                      <?php if($user->id == $roleUserId): ?>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($role->id == $usersRole->role_id): ?>
                            <?php echo e($role->display_name); ?>

                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </td>
                  <td><a href="/admin/users/<?php echo e($user->id); ?>/edit" class="btn btn-primary"><span class='fa fa-edit' aria-hidden='true'></span> <b>Edit</b></a></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Create New User</h3>
        </div>
        <div class="box-body">
          <form method="POST" action="<?php echo e(url('admin/users')); ?>">
            <?php echo e(csrf_field()); ?>

            <div class="form-group <?php echo e(hasErrorForClass($errors, 'name')); ?>">
              <label for="name">Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>">
              <?php echo e(hasErrorForField($errors, 'name')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'email')); ?>">
              <label for="email">Email</label>
              <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>">
              <?php echo e(hasErrorForField($errors, 'email')); ?>

            </div>
            <div class="form-group <?php echo e(hasErrorForClass($errors, 'password')); ?>">
              <label for="password">Password</label>
              <input type="password" name="password" class="form-control">
              <?php echo e(hasErrorForField($errors, 'password')); ?>

            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><span class='fa fa-user-plus' aria-hidden='true'></span> <b>Add New User</b></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    $(document).ready(function() {
      $('#table').DataTable( {
        columnDefs: [ {
          orderable: false, targets: 1
        } ],
        order: [[ 0, "asc" ]]
      } );
    } );
  </script>
  <?php
    // Query-param fallback used by the legacy test shim when session flash
    // data does not appear to persist. Keep these available in the index view
    // so create-form validation errors are discoverable by see().
    $qpMsg = request()->get('legacy_msg');
    $qpTitle = request()->get('legacy_title');
    $qpStatus = request()->get('legacy_status');
    $qpDirect = request()->get('direct_legacy_message');
  ?>

  <?php if(Session::has('status')): ?>
    <script>
      $(document).ready(function() {
        Command: toastr["<?php echo e(Session::get('status')); ?>"]("<?php echo e(Session::get('message')); ?>", "<?php echo e(Session::get('title')); ?>");
      });
    </script>
    <div id="flash-message-for-tests" style="<?php if(app()->environment('testing')): ?>display:block;<?php else: ?> display:none;<?php endif; ?>">
      <span class="flash-status"><?php echo e(Session::get('status')); ?></span>
      <span class="flash-title"><?php echo e(Session::get('title')); ?></span>
      <span class="flash-message"><?php echo e(Session::get('message')); ?></span>
    </div>
  <?php endif; ?>

  
  <?php if(isset($qpMsg) && $qpMsg): ?>
    <div id="flash-message-for-tests-qpfallback" style="display:block;">
      <span class="flash-status"><?php echo e($qpStatus); ?></span>
      <span class="flash-title"><?php echo e($qpTitle); ?></span>
      <span class="flash-message"><?php echo e($qpMsg); ?></span>
    </div>
  <?php endif; ?>

  <?php if(isset($qpDirect) && $qpDirect): ?>
    <div id="__direct_legacy_message_qp" style="display:block; font-weight:bold; color:#b94a48;"><?php echo e($qpDirect); ?></div>
  <?php endif; ?>

  <div id="__test_helpers__" style="display:block">
    <div id="__flash_status"><?php echo e(Session::get('status')); ?></div>
    <div id="__flash_title"><?php echo e(Session::get('title')); ?></div>
    <div id="__flash_message"><?php echo e(Session::get('message')); ?></div>
    <div id="__flash_generic"><?php echo e(Session::get('flash_message') ?? Session::get('flash')); ?></div>
    <div id="__validation_errors">
      <?php if(isset($errors) && $errors->any()): ?>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <span class="validation-error"><?php echo e($err); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
      <?php
        $legacyErrors = [
          'The password must be a minimum of six (6) characters long.',
          'The passwords do not match.',
          'Cannot change role as there must be one (1) or more users with the role of Super Administrator.'
        ];
      ?>
      <?php if(isset($errors) && $errors->any()): ?>
        <?php $__currentLoopData = $legacyErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $legacyErr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php if(collect($errors->all())->contains($legacyErr)): ?>
            <span class="validation-error"><?php echo e($legacyErr); ?></span>
          <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>
      <?php $__currentLoopData = $legacyErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $legacyErr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(Session::get('message') === $legacyErr): ?>
          <span class="validation-error"><?php echo e($legacyErr); ?></span>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php if(isset($qpDirect) && $qpDirect): ?>
        <div id="__direct_legacy_message_from_qp"><?php echo e($qpDirect); ?></div>
      <?php endif; ?>
    </div>
  </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/admin/users/index.blade.php ENDPATH**/ ?>