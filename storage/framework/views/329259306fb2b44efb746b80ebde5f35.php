

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/dashboard-widgets.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('main-content'); ?>
<?php echo $__env->make('components.loading-overlay', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', ['super-admin', 'admin'])): ?>
		<!-- Quick summary cards -->
	<section class="content">
		<div class="row mb-3">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body d-flex justify-content-between align-items-center">
						<div>
							<strong>Total Assets:</strong>
							<span class="ml-2"><?php echo e(\App\Asset::count()); ?></span>
						<div>
							<strong>Open Tickets:</strong>
							<span class="ml-2"><?php echo e(\App\Ticket::where('ticket_status_id', '!=', \App\TicketsStatus::where('status', 'Closed')->value('id'))->count() ?? '\N/A'); ?></span>
						</div>
						</div>
						<div>
							<strong>Recent Movements:</strong>
							<span class="ml-2"><?php echo e(isset($movements) ? $movements->count() : 0); ?></span>
						</div>
						<div class="text-muted small text-right">
							Server time: <?php echo e(now()->format('Y-m-d H:i:s')); ?>

							<br>
							<a href="<?php echo e(Route::has('reports.index') ? route('reports.index') : url('/reports')); ?>" class="btn btn-sm btn-outline-primary mt-1">Reports</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	
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
								<span class="ml-2"><?php echo e(\App\Ticket::where('ticket_status_id', '!=', \App\TicketsStatus::where('status', 'Closed')->value('id'))->count() ?? '\N/A'); ?></span>
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
		</section>
	<?php endif; ?>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/home.blade.php ENDPATH**/ ?>