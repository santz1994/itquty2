

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('tickets.user-create')); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Buat Tiket Baru
                    </a>
                </div>
            </div>
            
            <!-- Filter Form -->
            <div class="box-body">
                <form method="GET" action="<?php echo e(route('tickets.user-index')); ?>" class="form-inline" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="search">Cari:</label>
                        <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>" placeholder="Kode tiket atau judul...">
                    </div>
                    
                    <div class="form-group" style="margin-left: 15px;">
                        <label for="status">Status:</label>
                        <select class="form-control" name="status">
                            <option value="">Semua Status</option>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->id); ?>" <?php echo e(request('status') == $status->id ? 'selected' : ''); ?>>
                                    <?php echo e($status->status); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="margin-left: 15px;">
                        <i class="fa fa-search"></i> Filter
                    </button>
                    
                    <a href="<?php echo e(route('tickets.user-index')); ?>" class="btn btn-default" style="margin-left: 5px;">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-body">
                <?php if($tickets->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Tiket</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Prioritas</th>
                                    <th>Status</th>
                                    <th>Aset</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($ticket->ticket_code); ?></strong>
                                        </td>
                                        <td><?php echo e($ticket->subject); ?></td>
                                        <td>
                                            <span class="label label-info"><?php echo e($ticket->ticket_type->type ?? 'N/A'); ?></span>
                                        </td>
                                        <td>
                                            <span class="label 
                                                <?php if($ticket->ticket_priority->priority ?? '' == 'Urgent'): ?>
                                                    label-danger
                                                <?php elseif($ticket->ticket_priority->priority ?? '' == 'High'): ?>
                                                    label-warning
                                                <?php elseif($ticket->ticket_priority->priority ?? '' == 'Normal'): ?>
                                                    label-primary
                                                <?php else: ?>
                                                    label-success
                                                <?php endif; ?>
                                            ">
                                                <?php echo e($ticket->ticket_priority->priority ?? 'Normal'); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="label 
                                                <?php if($ticket->ticket_status->status ?? '' == 'Open'): ?>
                                                    label-info
                                                <?php elseif($ticket->ticket_status->status ?? '' == 'In Progress'): ?>
                                                    label-warning
                                                <?php elseif($ticket->ticket_status->status ?? '' == 'Resolved'): ?>
                                                    label-success
                                                <?php else: ?>
                                                    label-default
                                                <?php endif; ?>
                                            ">
                                                <?php echo e($ticket->ticket_status->status ?? 'Unknown'); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($ticket->asset): ?>
                                                <small><?php echo e($ticket->asset->asset_tag); ?></small>
                                            <?php else: ?>
                                                <em>Tidak ada</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?php echo e($ticket->created_at->format('d M Y H:i')); ?></small>
                                            <?php if($ticket->is_overdue): ?>
                                                <br><span class="label label-danger">Terlambat</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('tickets.user-show', $ticket->id)); ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="text-center">
                        <?php echo e($tickets->appends(request()->query())->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center" style="padding: 50px;">
                        <h4>Belum ada tiket</h4>
                        <p>Anda belum memiliki tiket. <a href="<?php echo e(route('tickets.user-create')); ?>">Buat tiket pertama Anda</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert('<?php echo e(session('success')); ?>');
        });
    </script>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\tickets\user\index.blade.php ENDPATH**/ ?>