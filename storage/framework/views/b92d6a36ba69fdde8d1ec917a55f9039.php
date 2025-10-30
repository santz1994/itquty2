

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-8">
        <!-- Ticket Information -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('tickets.user-index')); ?>" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali ke Daftar Tiket
                    </a>
                </div>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong><i class="fa fa-ticket"></i> Kode Tiket:</strong>
                        <p class="text-muted"><?php echo e($ticket->ticket_code); ?></p>
                        
                        <strong><i class="fa fa-calendar"></i> Tanggal Dibuat:</strong>
                        <p class="text-muted"><?php echo e($ticket->created_at->format('d F Y, H:i')); ?> WIB</p>
                        
                        <strong><i class="fa fa-tag"></i> Kategori:</strong>
                        <p class="text-muted">
                            <span class="label label-info"><?php echo e($ticket->ticket_type->type ?? 'N/A'); ?></span>
                        </p>
                        
                        <strong><i class="fa fa-exclamation-triangle"></i> Prioritas:</strong>
                        <p class="text-muted">
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
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <strong><i class="fa fa-info-circle"></i> Status:</strong>
                        <p class="text-muted">
                            <span class="label 
                                <?php if($ticket->ticket_status->status ?? '' == 'Open'): ?>
                                    label-info
                                <?php elseif($ticket->ticket_status->status ?? '' == 'In Progress'): ?>
                                    label-warning
                                <?php elseif($ticket->ticket_status->status ?? '' == 'Resolved'): ?>
                                    label-success
                                <?php elseif($ticket->ticket_status->status ?? '' == 'Closed'): ?>
                                    label-default
                                <?php else: ?>
                                    label-default
                                <?php endif; ?>
                            ">
                                <?php echo e($ticket->ticket_status->status ?? 'Unknown'); ?>

                            </span>
                        </p>
                        
                        <?php if($ticket->assignedTo): ?>
                            <strong><i class="fa fa-user"></i> Teknisi yang Menangani:</strong>
                            <p class="text-muted"><?php echo e($ticket->assignedTo->name); ?></p>
                        <?php else: ?>
                            <strong><i class="fa fa-user"></i> Teknisi:</strong>
                            <p class="text-muted"><em>Belum ditugaskan</em></p>
                        <?php endif; ?>
                        
                        <?php if($ticket->location): ?>
                            <strong><i class="fa fa-map-marker"></i> Lokasi:</strong>
                            <p class="text-muted"><?php echo e($ticket->location->location_name); ?></p>
                        <?php endif; ?>
                        
                        <?php if($ticket->asset): ?>
                            <strong><i class="fa fa-desktop"></i> Aset:</strong>
                            <p class="text-muted">
                                <?php echo e($ticket->asset->asset_tag); ?>

                                <?php if($ticket->asset->model): ?>
                                    - <?php echo e($ticket->asset->model->model_name); ?>

                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <hr>
                
                <strong><i class="fa fa-edit"></i> Judul Masalah:</strong>
                <p class="text-muted"><?php echo e($ticket->subject); ?></p>
                
                <strong><i class="fa fa-comment"></i> Deskripsi:</strong>
                <div class="well well-sm">
                    <?php echo nl2br(e($ticket->description)); ?>

                </div>
                
                <!-- SLA Information -->
                <?php if($ticket->sla_due): ?>
                    <div class="alert 
                        <?php if($ticket->is_overdue): ?>
                            alert-danger
                        <?php elseif($ticket->sla_due->diffInHours(now()) <= 2): ?>
                            alert-warning
                        <?php else: ?>
                            alert-info
                        <?php endif; ?>
                    ">
                        <i class="fa fa-clock-o"></i> 
                        <strong>Target Penyelesaian:</strong> <?php echo e($ticket->sla_due->format('d F Y, H:i')); ?> WIB
                        <?php if($ticket->is_overdue): ?>
                            <br><small>Tiket ini sudah melewati target waktu penyelesaian.</small>
                        <?php else: ?>
                            <br><small><?php echo e($ticket->time_to_sla); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Resolution (if resolved) -->
                <?php if($ticket->resolved_at && $ticket->resolution): ?>
                    <div class="alert alert-success">
                        <h4><i class="fa fa-check-circle"></i> Tiket Diselesaikan</h4>
                        <p><strong>Tanggal Selesai:</strong> <?php echo e($ticket->resolved_at->format('d F Y, H:i')); ?> WIB</p>
                        <p><strong>Solusi:</strong></p>
                        <div class="well well-sm">
                            <?php echo nl2br(e($ticket->resolution)); ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Ticket Timeline -->
        <?php if($ticketEntries->count() > 0): ?>
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-history"></i> Riwayat Aktivitas</h3>
                </div>
                <div class="box-body">
                    <ul class="timeline">
                        <?php $__currentLoopData = $ticketEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <i class="fa fa-comment bg-blue"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fa fa-clock-o"></i> <?php echo e($entry->created_at->format('d M Y H:i')); ?>

                                    </span>
                                    <h3 class="timeline-header">
                                        <strong><?php echo e($entry->user->name ?? 'System'); ?></strong>
                                        <?php if($entry->user->hasRole('admin') || $entry->user->hasRole('super-admin')): ?>
                                            <span class="label label-primary">Teknisi</span>
                                        <?php endif; ?>
                                    </h3>
                                    <div class="timeline-body">
                                        <?php echo nl2br(e($entry->body)); ?>

                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <i class="fa fa-plus bg-green"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fa fa-clock-o"></i> <?php echo e($ticket->created_at->format('d M Y H:i')); ?>

                                </span>
                                <h3 class="timeline-header">
                                    <strong><?php echo e($ticket->user->name); ?></strong> membuat tiket
                                </h3>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Quick Info -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-info-circle"></i> Informasi</h3>
            </div>
            <div class="box-body">
                <?php if($ticket->ticket_status->status == 'Open'): ?>
                    <div class="alert alert-info">
                        <i class="fa fa-clock-o"></i> Tiket Anda sedang menunggu untuk ditugaskan ke teknisi.
                    </div>
                <?php elseif($ticket->ticket_status->status == 'In Progress'): ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-cogs"></i> Teknisi sedang menangani tiket Anda.
                    </div>
                <?php elseif($ticket->ticket_status->status == 'Pending'): ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-pause"></i> Tiket menunggu informasi atau aksi dari Anda.
                    </div>
                <?php elseif($ticket->ticket_status->status == 'Resolved'): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check"></i> Tiket telah diselesaikan!
                    </div>
                <?php endif; ?>
                
                <h5><strong>Yang Perlu Anda Ketahui:</strong></h5>
                <ul>
                    <li>Anda akan mendapat notifikasi email untuk setiap update</li>
                    <li>Jika ada informasi tambahan, teknisi akan menghubungi Anda</li>
                    <li>Tiket akan otomatis ditutup 3 hari setelah diselesaikan</li>
                </ul>
            </div>
        </div>
        
        <!-- Asset Information (if any) -->
        <?php if($ticket->asset): ?>
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-desktop"></i> Informasi Aset</h3>
                </div>
                <div class="box-body">
                    <p><strong>Tag Aset:</strong> <?php echo e($ticket->asset->asset_tag); ?></p>
                    <?php if($ticket->asset->model): ?>
                        <p><strong>Model:</strong> <?php echo e($ticket->asset->model->model_name); ?></p>
                    <?php endif; ?>
                    <?php if($ticket->asset->serial_number): ?>
                        <p><strong>Serial Number:</strong> <?php echo e($ticket->asset->serial_number); ?></p>
                    <?php endif; ?>
                    <p><strong>Status:</strong> 
                        <span class="label label-info"><?php echo e($ticket->asset->status->name ?? 'Unknown'); ?></span>
                    </p>
                    
                    <a href="<?php echo e(route('assets.user-show', $ticket->asset->id)); ?>" class="btn btn-sm btn-default">
                        <i class="fa fa-eye"></i> Lihat Detail Aset
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Contact Support -->
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-phone"></i> Butuh Bantuan?</h3>
            </div>
            <div class="box-body">
                <?php if($ticket->ticket_priority->priority == 'Urgent'): ?>
                    <div class="alert alert-danger">
                        <strong>Masalah Urgent?</strong><br>
                        Hubungi helpdesk segera di Ext. 123
                    </div>
                <?php endif; ?>
                
                <p>Jika Anda memiliki informasi tambahan atau pertanyaan:</p>
                <ul class="list-unstyled">
                    <li><i class="fa fa-phone"></i> <strong>Helpdesk:</strong> Ext. 123</li>
                    <li><i class="fa fa-envelope"></i> <strong>Email:</strong> helpdesk@company.com</li>
                </ul>
                
                <small class="text-muted">Saat menghubungi, sebutkan kode tiket: <strong><?php echo e($ticket->ticket_code); ?></strong></small>
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\tickets\user\show.blade.php ENDPATH**/ ?>