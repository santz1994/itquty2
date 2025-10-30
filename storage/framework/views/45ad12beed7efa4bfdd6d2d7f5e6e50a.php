

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?php echo e($pageTitle); ?></h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo e(route('tickets.user-create')); ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Laporkan Masalah
                    </a>
                </div>
            </div>
            
            <!-- Filter Form -->
            <div class="box-body">
                <form method="GET" action="<?php echo e(route('assets.user-index')); ?>" class="form-inline" style="margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="search">Cari:</label>
                        <input type="text" class="form-control" name="search" value="<?php echo e(request('search')); ?>" 
                               placeholder="Tag aset, serial number, atau model...">
                    </div>
                    
                    <div class="form-group" style="margin-left: 15px;">
                        <label for="status">Status:</label>
                        <select class="form-control" name="status">
                            <option value="">Semua Status</option>
                            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status->id); ?>" <?php echo e(request('status') == $status->id ? 'selected' : ''); ?>>
                                    <?php echo e($status->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="margin-left: 15px;">
                        <i class="fa fa-search"></i> Filter
                    </button>
                    
                    <a href="<?php echo e(route('assets.user-index')); ?>" class="btn btn-default" style="margin-left: 5px;">
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
                <?php if($assets->count() > 0): ?>
                    <div class="row">
                        <?php $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="box box-widget widget-user-2">
                                    <div class="widget-user-header 
                                        <?php if($asset->status->name == 'Active'): ?>
                                            bg-green
                                        <?php elseif($asset->status->name == 'In Repair' || $asset->status->name == 'Maintenance'): ?>
                                            bg-yellow
                                        <?php elseif($asset->status->name == 'Retired' || $asset->status->name == 'Disposed'): ?>
                                            bg-red
                                        <?php else: ?>
                                            bg-blue
                                        <?php endif; ?>
                                    ">
                                        <div class="widget-user-image">
                                            <i class="fa 
                                                <?php if(str_contains(strtolower($asset->model->model_name ?? ''), 'laptop')): ?>
                                                    fa-laptop
                                                <?php elseif(str_contains(strtolower($asset->model->model_name ?? ''), 'desktop')): ?>
                                                    fa-desktop
                                                <?php elseif(str_contains(strtolower($asset->model->model_name ?? ''), 'printer')): ?>
                                                    fa-print
                                                <?php elseif(str_contains(strtolower($asset->model->model_name ?? ''), 'server')): ?>
                                                    fa-server
                                                <?php else: ?>
                                                    fa-cube
                                                <?php endif; ?>
                                                fa-3x text-white
                                            "></i>
                                        </div>
                                        <h3 class="widget-user-username"><?php echo e($asset->asset_tag); ?></h3>
                                        <h5 class="widget-user-desc"><?php echo e($asset->model->model_name ?? 'Model tidak diketahui'); ?></h5>
                                    </div>
                                    
                                    <div class="box-footer no-padding">
                                        <ul class="nav nav-stacked">
                                            <li>
                                                <span class="text-muted">
                                                    <i class="fa fa-barcode margin-r-5"></i> Serial: 
                                                    <?php echo e($asset->serial_number ?: 'N/A'); ?>

                                                </span>
                                            </li>
                                            <li>
                                                <span class="text-muted">
                                                    <i class="fa fa-calendar margin-r-5"></i> Dibeli: 
                                                    <?php echo e($asset->purchase_date ? $asset->purchase_date->format('d M Y') : 'N/A'); ?>

                                                </span>
                                            </li>
                                            <li>
                                                <span class="text-muted">
                                                    <i class="fa fa-shield margin-r-5"></i> Garansi: 
                                                    <?php if($asset->warranty_end_date): ?>
                                                        <?php if($asset->warranty_end_date->isFuture()): ?>
                                                            <span class="text-green">Berlaku hingga <?php echo e($asset->warranty_end_date->format('d M Y')); ?></span>
                                                        <?php else: ?>
                                                            <span class="text-red">Expired <?php echo e($asset->warranty_end_date->format('d M Y')); ?></span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </span>
                                            </li>
                                            <?php if($asset->location): ?>
                                                <li>
                                                    <span class="text-muted">
                                                        <i class="fa fa-map-marker margin-r-5"></i> Lokasi: 
                                                        <?php echo e($asset->location->location_name); ?>

                                                    </span>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                        
                                        <div class="box-footer">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <a href="<?php echo e(route('assets.user-show', $asset->id)); ?>" class="btn btn-default btn-block">
                                                        <i class="fa fa-eye"></i> Detail
                                                    </a>
                                                </div>
                                                <div class="col-sm-6">
                                                    <a href="<?php echo e(route('tickets.user-create', ['asset_id' => $asset->id])); ?>" class="btn btn-warning btn-block">
                                                        <i class="fa fa-exclamation-triangle"></i> Laporkan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="text-center">
                        <?php echo e($assets->appends(request()->query())->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center" style="padding: 50px;">
                        <i class="fa fa-cube fa-5x text-muted"></i>
                        <h4>Tidak ada aset yang ditugaskan</h4>
                        <p>Saat ini tidak ada aset yang ditugaskan kepada Anda. Hubungi administrator untuk informasi lebih lanjut.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-info-circle"></i> Informasi Penting</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <h5><i class="fa fa-exclamation-triangle text-warning"></i> Melaporkan Masalah</h5>
                        <p>Jika mengalami masalah dengan aset Anda, klik tombol "Laporkan" atau buat tiket baru melalui menu tiket.</p>
                    </div>
                    <div class="col-md-4">
                        <h5><i class="fa fa-shield text-success"></i> Menjaga Aset</h5>
                        <p>Jaga aset dengan baik. Laporkan kerusakan atau kehilangan segera kepada IT Support.</p>
                    </div>
                    <div class="col-md-4">
                        <h5><i class="fa fa-phone text-info"></i> Kontak Darurat</h5>
                        <p>Untuk masalah urgent, hubungi helpdesk di Ext. 123 atau WhatsApp 08XX-XXXX-XXXX.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\assets\user\index.blade.php ENDPATH**/ ?>