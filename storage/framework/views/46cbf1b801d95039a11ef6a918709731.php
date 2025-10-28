<div class="col-md-4">
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Recent Overdue Tickets</h3>
            <div class="box-tools pull-right">
                <a href="<?php echo e(route('tickets.index', ['status' => 'overdue'])); ?>" class="btn btn-xs btn-danger">View All</a>
            </div>
        </div>
        <div class="box-body">
            <?php if(isset($overview['recent_overdue_tickets']) && count($overview['recent_overdue_tickets']) > 0): ?>
                <ul class="list-group">
                    <?php $__currentLoopData = $overview['recent_overdue_tickets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item">
                            <a href="<?php echo e(route('tickets.show', $t->id)); ?>"><?php echo e($t->ticket_code); ?> - <?php echo e(\Illuminate\Support\Str::limit($t->subject, 40)); ?></a>
                            <span class="pull-right text-muted small">Due <?php echo e($t->due_date ? \Carbon\Carbon::parse($t->due_date)->diffForHumans() : 'Unknown'); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php else: ?>
                <p class="text-muted">No overdue tickets</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views/management/widgets/overdue-tickets.blade.php ENDPATH**/ ?>