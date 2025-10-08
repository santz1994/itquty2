

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Tiket Belum Ditangani</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-toggle="tooltip" title="Refresh" onclick="location.reload()">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <?php if($tickets->count() > 0): ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Ada <strong><?php echo e($tickets->count()); ?></strong> tiket yang belum ditangani. 
                    Klik tombol <strong>"Ambil"</strong> untuk menangani tiket.
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="120">Kode Tiket</th>
                                <th>Pengirim</th>
                                <th>Lokasi</th>
                                <th>Prioritas</th>
                                <th>Tipe</th>
                                <th>Subjek</th>
                                <th>Aset</th>
                                <th width="100">Dibuat</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <span class="label label-default"><?php echo e($ticket->ticket_code ?? 'TIK-'.date('Ymd').'-'.str_pad($ticket->id, 3, '0', STR_PAD_LEFT)); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo e($ticket->user->name); ?></strong><br>
                                    <small class="text-muted"><?php echo e($ticket->user->email); ?></small>
                                </td>
                                <td><?php echo e($ticket->location->location_name ?? '-'); ?></td>
                                <td>
                                    <?php if($ticket->ticket_priority): ?>
                                        <?php if($ticket->ticket_priority->priority == 'Critical'): ?>
                                            <span class="label label-danger">
                                        <?php elseif($ticket->ticket_priority->priority == 'High'): ?>
                                            <span class="label label-warning">
                                        <?php elseif($ticket->ticket_priority->priority == 'Medium'): ?>
                                            <span class="label label-info">
                                        <?php else: ?>
                                            <span class="label label-success">
                                        <?php endif; ?>
                                        <?php echo e($ticket->ticket_priority->priority); ?></span>
                                    <?php else: ?>
                                        <span class="label label-default">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($ticket->ticket_type->type ?? '-'); ?></td>
                                <td>
                                    <strong><?php echo e(\Illuminate\Support\Str::limit($ticket->subject, 40)); ?></strong>
                                    <?php if(strlen($ticket->description) > 0): ?>
                                        <br><small class="text-muted"><?php echo e(\Illuminate\Support\Str::limit($ticket->description, 60)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($ticket->asset): ?>
                                        <span class="label label-primary"><?php echo e($ticket->asset->asset_tag); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo e($ticket->created_at->format('d/m H:i')); ?></small>
                                    <br>
                                    <small class="text-muted"><?php echo e($ticket->created_at->diffForHumans()); ?></small>
                                </td>
                                <td>
                                    <form method="POST" action="<?php echo e(route('tickets.self-assign', $ticket)); ?>" style="display: inline;">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-success btn-xs" 
                                                onclick="return confirm('Apakah Anda yakin ingin mengambil tiket ini untuk ditangani?')">
                                            <i class="fa fa-hand-grab-o"></i> Ambil
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> Tidak ada tiket yang belum ditangani. Semua tiket sudah di-assign!
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Auto refresh setiap 30 detik -->
<script>
setTimeout(function(){
    location.reload();
}, 30000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/tickets/unassigned.blade.php ENDPATH**/ ?>