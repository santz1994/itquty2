

<?php $__env->startSection('main-content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Catat Aktivitas Harian</h3>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('daily-activities.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="activity_date">Tanggal Aktivitas <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="activity_date" name="activity_date" 
                                           value="<?php echo e(old('activity_date', $today)); ?>" required max="<?php echo e(date('Y-m-d')); ?>">
                                    <small class="form-text text-muted">Tanggal tidak boleh lebih dari hari ini</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="activity_type">Tipe Aktivitas <span class="text-danger">*</span></label>
                                    <select class="form-control" id="activity_type" name="activity_type" required>
                                        <option value="">Pilih Tipe Aktivitas</option>
                                        <option value="ticket_handling" <?php echo e(old('activity_type') == 'ticket_handling' ? 'selected' : ''); ?>>
                                            Penanganan Ticket
                                        </option>
                                        <option value="asset_management" <?php echo e(old('activity_type') == 'asset_management' ? 'selected' : ''); ?>>
                                            Manajemen Asset
                                        </option>
                                        <option value="user_support" <?php echo e(old('activity_type') == 'user_support' ? 'selected' : ''); ?>>
                                            Dukungan User
                                        </option>
                                        <option value="system_maintenance" <?php echo e(old('activity_type') == 'system_maintenance' ? 'selected' : ''); ?>>
                                            Maintenance Sistem
                                        </option>
                                        <option value="documentation" <?php echo e(old('activity_type') == 'documentation' ? 'selected' : ''); ?>>
                                            Dokumentasi
                                        </option>
                                        <option value="training" <?php echo e(old('activity_type') == 'training' ? 'selected' : ''); ?>>
                                            Pelatihan
                                        </option>
                                        <option value="meeting" <?php echo e(old('activity_type') == 'meeting' ? 'selected' : ''); ?>>
                                            Meeting/Rapat
                                        </option>
                                        <option value="project_work" <?php echo e(old('activity_type') == 'project_work' ? 'selected' : ''); ?>>
                                            Pekerjaan Proyek
                                        </option>
                                        <option value="monitoring" <?php echo e(old('activity_type') == 'monitoring' ? 'selected' : ''); ?>>
                                            Monitoring Sistem
                                        </option>
                                        <option value="other" <?php echo e(old('activity_type') == 'other' ? 'selected' : ''); ?>>
                                            Lainnya
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="duration_minutes">Durasi (menit) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" 
                                           value="<?php echo e(old('duration_minutes')); ?>" required min="1" max="1440">
                                    <small class="form-text text-muted">Maksimal 1440 menit (24 jam)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi Aktivitas <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo e(old('description')); ?></textarea>
                            <small class="form-text text-muted">
                                Jelaskan apa yang dikerjakan secara detail:
                                <ul>
                                    <li>Apa yang dilakukan?</li>
                                    <li>Dengan siapa/untuk siapa?</li>
                                    <li>Hasil yang dicapai</li>
                                    <li>Tools/software yang digunakan</li>
                                </ul>
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_time">Waktu Mulai</label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" 
                                           value="<?php echo e(old('start_time')); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_time">Waktu Selesai</label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" 
                                           value="<?php echo e(old('end_time')); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location">Lokasi Kerja</label>
                            <select class="form-control" id="location" name="location">
                                <option value="">Pilih Lokasi</option>
                                <option value="office" <?php echo e(old('location') == 'office' ? 'selected' : ''); ?>>
                                    Kantor
                                </option>
                                <option value="remote" <?php echo e(old('location') == 'remote' ? 'selected' : ''); ?>>
                                    Remote/WFH
                                </option>
                                <option value="client_site" <?php echo e(old('location') == 'client_site' ? 'selected' : ''); ?>>
                                    Lokasi Client/User
                                </option>
                                <option value="field_work" <?php echo e(old('location') == 'field_work' ? 'selected' : ''); ?>>
                                    Pekerjaan Lapangan
                                </option>
                                <option value="other" <?php echo e(old('location') == 'other' ? 'selected' : ''); ?>>
                                    Lainnya
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="technologies_used">Teknologi/Tools yang Digunakan</label>
                            <input type="text" class="form-control" id="technologies_used" name="technologies_used" 
                                   value="<?php echo e(old('technologies_used')); ?>" maxlength="500">
                            <small class="form-text text-muted">
                                Pisahkan dengan koma. Contoh: Windows Server, Active Directory, PowerShell
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="outcome_achieved">Hasil yang Dicapai</label>
                            <textarea class="form-control" id="outcome_achieved" name="outcome_achieved" 
                                      rows="3"><?php echo e(old('outcome_achieved')); ?></textarea>
                            <small class="form-text text-muted">
                                Apa hasil konkret dari aktivitas ini? Masalah apa yang terselesaikan?
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="challenges_faced">Tantangan yang Dihadapi</label>
                            <textarea class="form-control" id="challenges_faced" name="challenges_faced" 
                                      rows="2"><?php echo e(old('challenges_faced')); ?></textarea>
                            <small class="form-text text-muted">
                                Adakah kesulitan atau hambatan yang ditemui?
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="notes">Catatan Tambahan</label>
                            <textarea class="form-control" id="notes" name="notes" 
                                      rows="2"><?php echo e(old('notes')); ?></textarea>
                            <small class="form-text text-muted">
                                Informasi tambahan yang mungkin berguna untuk laporan atau follow-up
                            </small>
                        </div>

                        <!-- Activity Classification -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>Klasifikasi Aktivitas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="priority_level">Level Prioritas</label>
                                            <select class="form-control" id="priority_level" name="priority_level">
                                                <option value="">Pilih Prioritas</option>
                                                <option value="routine" <?php echo e(old('priority_level') == 'routine' ? 'selected' : ''); ?>>
                                                    Routine - Aktivitas rutin harian
                                                </option>
                                                <option value="important" <?php echo e(old('priority_level') == 'important' ? 'selected' : ''); ?>>
                                                    Important - Penting untuk operasional
                                                </option>
                                                <option value="urgent" <?php echo e(old('priority_level') == 'urgent' ? 'selected' : ''); ?>>
                                                    Urgent - Memerlukan penanganan segera
                                                </option>
                                                <option value="critical" <?php echo e(old('priority_level') == 'critical' ? 'selected' : ''); ?>>
                                                    Critical - Kritis untuk bisnis
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="complexity_level">Level Kompleksitas</label>
                                            <select class="form-control" id="complexity_level" name="complexity_level">
                                                <option value="">Pilih Kompleksitas</option>
                                                <option value="simple" <?php echo e(old('complexity_level') == 'simple' ? 'selected' : ''); ?>>
                                                    Simple - Mudah dan straightforward
                                                </option>
                                                <option value="moderate" <?php echo e(old('complexity_level') == 'moderate' ? 'selected' : ''); ?>>
                                                    Moderate - Memerlukan beberapa langkah
                                                </option>
                                                <option value="complex" <?php echo e(old('complexity_level') == 'complex' ? 'selected' : ''); ?>>
                                                    Complex - Memerlukan analisis mendalam
                                                </option>
                                                <option value="expert" <?php echo e(old('complexity_level') == 'expert' ? 'selected' : ''); ?>>
                                                    Expert - Memerlukan keahlian khusus
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="billable_hours" 
                                               name="billable_hours" value="1" <?php echo e(old('billable_hours') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="billable_hours">
                                            Jam kerja yang dapat ditagihkan (billable hours)
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="requires_follow_up" 
                                               name="requires_follow_up" value="1" <?php echo e(old('requires_follow_up') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="requires_follow_up">
                                            Memerlukan tindak lanjut
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Aktivitas
                            </button>
                            <a href="<?php echo e(route('daily-activities.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-info" id="add-another">
                                <i class="fas fa-plus"></i> Simpan & Tambah Lagi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate duration based on start and end time
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const durationInput = document.getElementById('duration_minutes');
    
    function calculateDuration() {
        if (startTimeInput.value && endTimeInput.value) {
            const start = new Date('2000-01-01 ' + startTimeInput.value);
            const end = new Date('2000-01-01 ' + endTimeInput.value);
            
            if (end > start) {
                const diffMs = end - start;
                const diffMinutes = Math.round(diffMs / (1000 * 60));
                durationInput.value = diffMinutes;
            }
        }
    }
    
    startTimeInput.addEventListener('change', calculateDuration);
    endTimeInput.addEventListener('change', calculateDuration);
    
    // Set current time when focus on time inputs
    startTimeInput.addEventListener('focus', function() {
        if (!this.value) {
            const now = new Date();
            this.value = now.toTimeString().slice(0, 5);
        }
    });
    
    // Auto-suggest end time based on duration
    durationInput.addEventListener('input', function() {
        if (startTimeInput.value && this.value) {
            const start = new Date('2000-01-01 ' + startTimeInput.value);
            start.setMinutes(start.getMinutes() + parseInt(this.value));
            endTimeInput.value = start.toTimeString().slice(0, 5);
        }
    });
    
    // Handle "Save & Add Another" button
    document.getElementById('add-another').addEventListener('click', function() {
        const form = this.closest('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'add_another';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    });
    
    // Auto-fill common activities
    const activityTypeSelect = document.getElementById('activity_type');
    const descriptionTextarea = document.getElementById('description');
    
    activityTypeSelect.addEventListener('change', function() {
        if (!descriptionTextarea.value) { // Only if description is empty
            const templates = {
                'ticket_handling': 'Menangani ticket dari user terkait ',
                'asset_management': 'Melakukan manajemen asset ',
                'user_support': 'Memberikan dukungan kepada user untuk ',
                'system_maintenance': 'Melakukan maintenance sistem ',
                'documentation': 'Membuat/update dokumentasi untuk ',
                'training': 'Mengikuti/memberikan pelatihan tentang ',
                'meeting': 'Menghadiri meeting/rapat mengenai ',
                'project_work': 'Mengerjakan proyek ',
                'monitoring': 'Monitoring sistem/infrastruktur '
            };
            
            if (templates[this.value]) {
                descriptionTextarea.value = templates[this.value];
                descriptionTextarea.focus();
                // Move cursor to end
                descriptionTextarea.setSelectionRange(descriptionTextarea.value.length, descriptionTextarea.value.length);
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Project\ITQuty\Quty1\resources\views/daily-activities/create.blade.php ENDPATH**/ ?>