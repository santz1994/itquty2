

<?php $__env->startSection('main-content'); ?>
<div class="row">
    <div class="col-md-8">
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
                <form method="POST" action="<?php echo e(route('tickets.user-store')); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Kategori Masalah -->
                    <div class="form-group">
                        <label for="ticket_type_id">
                            <i class="fa fa-tag"></i> Kategori Masalah <span class="text-red">*</span>
                            <?php if($errors->has('ticket_type_id')): ?>
                                <small class="text-red"><?php echo e($errors->first('ticket_type_id')); ?></small>
                            <?php endif; ?>
                        </label>
                        <select class="form-control" name="ticket_type_id" required>
                            <option value="">-- Pilih Kategori Masalah --</option>
                            <?php $__currentLoopData = $ticketTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type->id); ?>" <?php echo e(old('ticket_type_id') == $type->id ? 'selected' : ''); ?>>
                                    <?php echo e($type->type); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Pilih kategori yang paling sesuai dengan masalah Anda</small>
                    </div>

                    <!-- Tingkat Prioritas -->
                    <div class="form-group">
                        <label for="ticket_priority_id">
                            <i class="fa fa-exclamation-triangle"></i> Tingkat Dampak <span class="text-red">*</span>
                            <?php if($errors->has('ticket_priority_id')): ?>
                                <small class="text-red"><?php echo e($errors->first('ticket_priority_id')); ?></small>
                            <?php endif; ?>
                        </label>
                        <select class="form-control" name="ticket_priority_id" required>
                            <option value="">-- Pilih Tingkat Dampak --</option>
                            <?php $__currentLoopData = $ticketPriorities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priority): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($priority->id); ?>" <?php echo e(old('ticket_priority_id') == $priority->id ? 'selected' : ''); ?>>
                                    <?php echo e($priority->priority); ?>

                                    <?php if($priority->priority == 'Urgent'): ?>
                                        - Sistem tidak bisa digunakan sama sekali
                                    <?php elseif($priority->priority == 'High'): ?>
                                        - Mengganggu pekerjaan secara signifikan
                                    <?php elseif($priority->priority == 'Normal'): ?>
                                        - Mengganggu tapi masih bisa bekerja
                                    <?php elseif($priority->priority == 'Low'): ?>
                                        - Gangguan kecil/permintaan enhancement
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Seberapa besar dampak masalah ini terhadap pekerjaan Anda?</small>
                    </div>

                    <!-- Aset yang Bermasalah -->
                    <div class="form-group">
                        <label for="asset_id">
                            <i class="fa fa-desktop"></i> Aset yang Bermasalah
                            <?php if($errors->has('asset_id')): ?>
                                <small class="text-red"><?php echo e($errors->first('asset_id')); ?></small>
                            <?php endif; ?>
                        </label>
                        <select class="form-control" name="asset_id">
                            <option value="">-- Tidak terkait dengan aset tertentu --</option>
                            <?php $__currentLoopData = $userAssets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($asset->id); ?>" <?php echo e(old('asset_id') == $asset->id ? 'selected' : ''); ?>>
                                    <?php echo e($asset->asset_tag); ?> - <?php echo e($asset->model->model_name ?? 'Model tidak diketahui'); ?>

                                    <?php if($asset->serial_number): ?>
                                        (S/N: <?php echo e($asset->serial_number); ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Pilih aset jika masalah terkait dengan perangkat tertentu</small>
                    </div>

                    <!-- Lokasi -->
                    <div class="form-group">
                        <label for="location_id">
                            <i class="fa fa-map-marker"></i> Lokasi
                            <?php if($errors->has('location_id')): ?>
                                <small class="text-red"><?php echo e($errors->first('location_id')); ?></small>
                            <?php endif; ?>
                        </label>
                        <select class="form-control" name="location_id">
                            <option value="">-- Pilih Lokasi (Opsional) --</option>
                            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($location->id); ?>" <?php echo e(old('location_id') == $location->id ? 'selected' : ''); ?>>
                                    <?php echo e($location->location_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Dimana masalah ini terjadi?</small>
                    </div>

                    <!-- Judul Masalah -->
                    <div class="form-group">
                        <label for="subject">
                            <i class="fa fa-edit"></i> Judul Masalah <span class="text-red">*</span>
                            <?php if($errors->has('subject')): ?>
                                <small class="text-red"><?php echo e($errors->first('subject')); ?></small>
                            <?php endif; ?>
                        </label>
                        <input type="text" class="form-control" name="subject" value="<?php echo e(old('subject')); ?>" 
                               placeholder="Contoh: Laptop tidak bisa menyala" maxlength="255" required>
                        <small class="text-muted">Berikan judul yang singkat dan jelas menggambarkan masalah</small>
                    </div>

                    <!-- Deskripsi Detail -->
                    <div class="form-group">
                        <label for="description">
                            <i class="fa fa-comment"></i> Deskripsi Detail <span class="text-red">*</span>
                            <?php if($errors->has('description')): ?>
                                <small class="text-red"><?php echo e($errors->first('description')); ?></small>
                            <?php endif; ?>
                        </label>
                        <textarea class="form-control" name="description" rows="6" 
                                  placeholder="Jelaskan masalah secara detail:&#10;- Apa yang sedang Anda lakukan saat masalah terjadi?&#10;- Pesan error apa yang muncul (jika ada)?&#10;- Sudah berapa lama masalah ini terjadi?&#10;- Langkah apa yang sudah Anda coba?" required><?php echo e(old('description')); ?></textarea>
                        <small class="text-muted">Semakin detail informasi yang Anda berikan, semakin cepat kami dapat membantu</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-paper-plane"></i> Kirim Tiket
                        </button>
                        <a href="<?php echo e(route('tickets.user-index')); ?>" class="btn btn-default btn-lg">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <?php if(count($errors)): ?>
            <div class="alert alert-danger">
                <h4><i class="fa fa-exclamation-triangle"></i> Mohon perbaiki kesalahan berikut:</h4>
                <ul style="margin-bottom: 0;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar dengan Tips -->
    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Tips Membuat Tiket</h3>
            </div>
            <div class="box-body">
                <h5><i class="fa fa-check"></i> Judul yang Baik:</h5>
                <ul>
                    <li>✅ "Laptop tidak bisa menyala"</li>
                    <li>✅ "Printer tidak bisa print warna"</li>
                    <li>❌ "Tolong bantu"</li>
                    <li>❌ "Ada masalah"</li>
                </ul>
                
                <h5><i class="fa fa-info-circle"></i> Informasi Penting:</h5>
                <ul>
                    <li>Jelaskan langkah-langkah yang sudah dicoba</li>
                    <li>Sertakan pesan error jika ada</li>
                    <li>Ceritakan kapan masalah mulai terjadi</li>
                    <li>Apakah masalah terjadi terus-menerus atau kadang-kadang</li>
                </ul>
                
                <h5><i class="fa fa-clock-o"></i> Tingkat Prioritas:</h5>
                <ul class="list-unstyled">
                    <li><span class="label label-danger">Urgent</span> - Sistem down total</li>
                    <li><span class="label label-warning">High</span> - Tidak bisa bekerja</li>
                    <li><span class="label label-primary">Normal</span> - Masih bisa bekerja</li>
                    <li><span class="label label-success">Low</span> - Bug kecil</li>
                </ul>
            </div>
        </div>
        
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-phone"></i> Kontak Darurat</h3>
            </div>
            <div class="box-body">
                <p>Untuk masalah <strong>URGENT</strong> yang memerlukan penanganan segera:</p>
                <ul class="list-unstyled">
                    <li><i class="fa fa-phone"></i> <strong>Telp:</strong> Ext. 123</li>
                    <li><i class="fa fa-whatsapp"></i> <strong>WhatsApp:</strong> 08XX-XXXX-XXXX</li>
                </ul>
                <small class="text-muted">Gunakan kontak darurat hanya untuk masalah yang benar-benar urgent</small>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Auto-select priority based on asset status
    $('select[name="asset_id"]').change(function() {
        var assetId = $(this).val();
        if (assetId) {
            // You can add AJAX call here to get asset details and suggest priority
        }
    });
    
    // Character counter for subject
    $('input[name="subject"]').on('input', function() {
        var maxLength = 255;
        var currentLength = $(this).val().length;
        var remaining = maxLength - currentLength;
        
        // Remove existing counter
        $(this).siblings('.char-counter').remove();
        
        // Add counter
        if (remaining < 50) {
            $(this).after('<small class="char-counter text-muted">' + remaining + ' karakter tersisa</small>');
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\quty2\resources\views\tickets\user\create.blade.php ENDPATH**/ ?>