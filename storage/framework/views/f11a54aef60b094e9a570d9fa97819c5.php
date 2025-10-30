

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Detail Aktivitas Harian</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('daily-activities.index')); ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit daily activities')): ?>
                            <a href="<?php echo e(route('daily-activities.edit', $dailyActivity->id)); ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Tanggal Aktivitas:</th>
                                    <td><?php echo e($dailyActivity->activity_date->format('d M Y')); ?></td>
                                </tr>
                                <tr>
                                    <th>Tipe Aktivitas:</th>
                                    <td>
                                        <?php
                                            $activityTypes = [
                                                'ticket_handling' => 'Penanganan Ticket',
                                                'asset_management' => 'Manajemen Asset',
                                                'user_support' => 'Dukungan User',
                                                'system_maintenance' => 'Maintenance Sistem',
                                                'training' => 'Pelatihan',
                                                'documentation' => 'Dokumentasi',
                                                'meeting' => 'Meeting/Rapat',
                                                'other' => 'Lainnya'
                                            ];
                                        ?>
                                        <span class="badge badge-info">
                                            <?php echo e($activityTypes[$dailyActivity->activity_type] ?? $dailyActivity->activity_type); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php
                                            $statusTypes = [
                                                'in_progress' => ['label' => 'Sedang Berlangsung', 'class' => 'badge-warning'],
                                                'completed' => ['label' => 'Selesai', 'class' => 'badge-success'],
                                                'paused' => ['label' => 'Ditunda', 'class' => 'badge-secondary'],
                                                'cancelled' => ['label' => 'Dibatalkan', 'class' => 'badge-danger']
                                            ];
                                            $status = $statusTypes[$dailyActivity->status] ?? ['label' => $dailyActivity->status, 'class' => 'badge-secondary'];
                                        ?>
                                        <span class="badge <?php echo e($status['class']); ?>">
                                            <?php echo e($status['label']); ?>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Judul Aktivitas:</th>
                                    <td><strong><?php echo e($dailyActivity->title); ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Waktu:</th>
                                    <td>
                                        <?php if($dailyActivity->start_time && $dailyActivity->end_time): ?>
                                            <?php echo e($dailyActivity->start_time->format('H:i')); ?> - <?php echo e($dailyActivity->end_time->format('H:i')); ?>

                                            <?php
                                                $duration = $dailyActivity->start_time->diff($dailyActivity->end_time);
                                                $hours = $duration->h;
                                                $minutes = $duration->i;
                                            ?>
                                            <small class="text-muted">
                                                (<?php echo e($hours); ?>j <?php echo e($minutes); ?>m)
                                            </small>
                                        <?php elseif($dailyActivity->start_time): ?>
                                            Mulai: <?php echo e($dailyActivity->start_time->format('H:i')); ?>

                                        <?php elseif($dailyActivity->end_time): ?>
                                            Selesai: <?php echo e($dailyActivity->end_time->format('H:i')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Waktu tidak tercatat</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Prioritas:</th>
                                    <td>
                                        <?php if($dailyActivity->is_priority): ?>
                                            <span class="badge badge-danger">
                                                <i class="fas fa-star"></i> Prioritas Tinggi
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-light">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Petugas:</th>
                                    <td><?php echo e($dailyActivity->user->name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <th>Asset Terkait:</th>
                                    <td>
                                        <?php if($dailyActivity->relatedAsset): ?>
                                            <a href="<?php echo e(route('assets.show', $dailyActivity->relatedAsset->id)); ?>" class="text-decoration-none">
                                                <span class="badge badge-outline-primary">
                                                    <?php echo e($dailyActivity->relatedAsset->asset_tag); ?> - <?php echo e($dailyActivity->relatedAsset->name); ?>

                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Ticket Terkait:</th>
                                    <td>
                                        <?php if($dailyActivity->relatedTicket): ?>
                                            <a href="<?php echo e(route('tickets.show', $dailyActivity->relatedTicket->id)); ?>" class="text-decoration-none">
                                                <span class="badge badge-outline-info">
                                                    #<?php echo e($dailyActivity->relatedTicket->id); ?> - <?php echo e(\Illuminate\Support\Str::limit($dailyActivity->relatedTicket->subject, 30)); ?>

                                                </span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat:</th>
                                    <td><?php echo e($dailyActivity->created_at->format('d M Y H:i')); ?></td>
                                </tr>
                                <tr>
                                    <th>Terakhir Update:</th>
                                    <td><?php echo e($dailyActivity->updated_at->format('d M Y H:i')); ?></td>
                                </tr>
                                <?php if($dailyActivity->created_at != $dailyActivity->updated_at): ?>
                                <tr>
                                    <th>Diupdate oleh:</th>
                                    <td><?php echo e($dailyActivity->updatedBy->name ?? 'System'); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>Deskripsi Aktivitas</h5>
                            <div class="card card-outline card-secondary">
                                <div class="card-body">
                                    <p class="mb-0"><?php echo e($dailyActivity->description); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($dailyActivity->notes): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Catatan Tambahan</h5>
                            <div class="card card-outline card-info">
                                <div class="card-body">
                                    <p class="mb-0"><?php echo e($dailyActivity->notes); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($dailyActivity->relatedAsset && $dailyActivity->relatedAsset->movements->count() > 0): ?>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>Riwayat Asset Terkait</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Tipe Movement</th>
                                            <th>Lokasi</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $dailyActivity->relatedAsset->movements->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($movement->created_at->format('d M Y')); ?></td>
                                            <td>
                                                <span class="badge badge-sm badge-secondary">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $movement->movement_type))); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($movement->location->name ?? 'N/A'); ?></td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($movement->notes, 50)); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <?php if($dailyActivity->relatedAsset->movements->count() > 5): ?>
                                <small class="text-muted">
                                    Menampilkan 5 dari <?php echo e($dailyActivity->relatedAsset->movements->count()); ?> movement terbaru
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit daily activities')): ?>
                                <a href="<?php echo e(route('daily-activities.edit', $dailyActivity->id)); ?>" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Aktivitas
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-right">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete daily activities')): ?>
                                <form action="<?php echo e(route('daily-activities.destroy', $dailyActivity->id)); ?>" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus aktivitas ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            <?php endif; ?>
                            <button onclick="window.print()" class="btn btn-outline-secondary">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
@media print {
    .card-header .card-tools,
    .card-footer,
    .btn {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .badge-outline-primary {
        border: 1px solid #007bff !important;
        color: #007bff !important;
    }
    
    .badge-outline-info {
        border: 1px solid #17a2b8 !important;
        color: #17a2b8 !important;
    }
}

.badge-outline-primary {
    background-color: transparent;
    border: 1px solid #007bff;
    color: #007bff;
}

.badge-outline-info {
    background-color: transparent;
    border: 1px solid #17a2b8;
    color: #17a2b8;
}

.table-borderless th,
.table-borderless td {
    border: none;
    padding: 0.5rem 0.75rem;
}

.table-borderless th {
    font-weight: 600;
    color: #495057;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views/daily-activities/show.blade.php ENDPATH**/ ?>