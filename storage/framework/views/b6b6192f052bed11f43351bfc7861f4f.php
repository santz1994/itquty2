

<?php $__env->startSection('main-content'); ?>
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
        </div>
        <div class="box-body">
          <ul class="timeline">
				    <!-- timeline time label -->
						<?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($movement->asset_id == $asset->id): ?>
							<?php $createdDate = \Carbon\Carbon::parse($movement->created_at);
              $asset = App\Asset::find($movement->asset_id); ?>
							<li class="time-label">
				        <span class="bg-aqua">
			            <?php echo e($createdDate->format('l, j F Y')); ?>

				        </span>
					    </li>
					    <!-- /.timeline-label -->

					    <!-- timeline item -->
					    <li>
				        <!-- timeline icon -->
				        <i class="fa fa-user bg-blue"></i>
				        <div class="timeline-item">
			            <span class="time"><i class="fa fa-clock-o"></i> <?php echo e($createdDate->format('H:i')); ?></span>

			            <h3 class="timeline-header"><?php echo e($movement->user->name); ?></h3>

			            <div class="timeline-body">
										<dl class="dl-horizontal">
                      <dt>Asset:</dt><dd><?php echo e($asset->asset_tag); ?></dd>
				              <dt>Model:</dt><dd><?php echo e($asset->model->manufacturer->name); ?> <?php echo e($asset->model->asset_model); ?></dd>
				              <dt>Location:</dt><dd><?php echo e($movement->location->location_name); ?></dd>
				              <dt>Status Applied:</dt><dd><?php echo e($movement->status->name); ?></dd>
										</dl>
			            </div>
			            <div class="timeline-footer">
			            </div>
				        </div>
				    	</li>
					    <!-- END timeline item -->
              <?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</ul>
          <div class="text-center"><a class="btn btn-primary" href="<?php echo e(URL::previous()); ?>">Back</a></div>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\movements\history.blade.php ENDPATH**/ ?>