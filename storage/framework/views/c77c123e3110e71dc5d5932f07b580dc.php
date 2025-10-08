

<?php $__env->startSection('main-content'); ?>
	<?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin', 'admin'])): ?>
		<div class="row">
			<div class="col-md-5 col-xs-12">
	      <div class="box box-primary">
	        <div class="box-header with-border">
	          <h3 class="box-title">Latest Movement Activity</h3>
	        </div>
	        <div class="box-body">
						<ul class="timeline">
					    <!-- timeline time label -->
							<?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/home.blade.php ENDPATH**/ ?>