@extends('layouts.app')

@section('main-content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Aktivitas Harian</h3>
                    <div class="card-tools">
                        <a href="{{ route('daily-activities.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('daily-activities.update', $dailyActivity->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="activity_date">Tanggal Aktivitas <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="activity_date" name="activity_date" 
                                           value="{{ old('activity_date', $dailyActivity->activity_date->format('Y-m-d')) }}" 
                                           required max="{{ date('Y-m-d') }}">
                                    <small class="form-text text-muted">Tanggal tidak boleh lebih dari hari ini</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="activity_type">Tipe Aktivitas <span class="text-danger">*</span></label>
                                    <select class="form-control" id="activity_type" name="activity_type" required>
                                        <option value="">Pilih Tipe Aktivitas</option>
                                        <option value="ticket_handling" {{ old('activity_type', $dailyActivity->activity_type) == 'ticket_handling' ? 'selected' : '' }}>
                                            Penanganan Ticket
                                        </option>
                                        <option value="asset_management" {{ old('activity_type', $dailyActivity->activity_type) == 'asset_management' ? 'selected' : '' }}>
                                            Manajemen Asset
                                        </option>
                                        <option value="user_support" {{ old('activity_type', $dailyActivity->activity_type) == 'user_support' ? 'selected' : '' }}>
                                            Dukungan User
                                        </option>
                                        <option value="system_maintenance" {{ old('activity_type', $dailyActivity->activity_type) == 'system_maintenance' ? 'selected' : '' }}>
                                            Maintenance Sistem
                                        </option>
                                        <option value="training" {{ old('activity_type', $dailyActivity->activity_type) == 'training' ? 'selected' : '' }}>
                                            Pelatihan
                                        </option>
                                        <option value="documentation" {{ old('activity_type', $dailyActivity->activity_type) == 'documentation' ? 'selected' : '' }}>
                                            Dokumentasi
                                        </option>
                                        <option value="meeting" {{ old('activity_type', $dailyActivity->activity_type) == 'meeting' ? 'selected' : '' }}>
                                            Meeting/Rapat
                                        </option>
                                        <option value="other" {{ old('activity_type', $dailyActivity->activity_type) == 'other' ? 'selected' : '' }}>
                                            Lainnya
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="in_progress" {{ old('status', $dailyActivity->status) == 'in_progress' ? 'selected' : '' }}>
                                            Sedang Berlangsung
                                        </option>
                                        <option value="completed" {{ old('status', $dailyActivity->status) == 'completed' ? 'selected' : '' }}>
                                            Selesai
                                        </option>
                                        <option value="paused" {{ old('status', $dailyActivity->status) == 'paused' ? 'selected' : '' }}>
                                            Ditunda
                                        </option>
                                        <option value="cancelled" {{ old('status', $dailyActivity->status) == 'cancelled' ? 'selected' : '' }}>
                                            Dibatalkan
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Judul Aktivitas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="{{ old('title', $dailyActivity->title) }}" required maxlength="255">
                                    <small class="form-text text-muted">Maksimal 255 karakter</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_time">Waktu Mulai</label>
                                    <input type="time" class="form-control" id="start_time" name="start_time" 
                                           value="{{ old('start_time', $dailyActivity->start_time ? $dailyActivity->start_time->format('H:i') : '') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_time">Waktu Selesai</label>
                                    <input type="time" class="form-control" id="end_time" name="end_time" 
                                           value="{{ old('end_time', $dailyActivity->end_time ? $dailyActivity->end_time->format('H:i') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi Aktivitas <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="4" required maxlength="1000">{{ old('description', $dailyActivity->description) }}</textarea>
                            <small class="form-text text-muted">Jelaskan detail aktivitas yang dilakukan (maksimal 1000 karakter)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="related_asset_id">Asset Terkait (Opsional)</label>
                                    <select class="form-control select2" id="related_asset_id" name="related_asset_id">
                                        <option value="">Pilih Asset (jika ada)</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->id }}" 
                                                {{ old('related_asset_id', $dailyActivity->related_asset_id) == $asset->id ? 'selected' : '' }}>
                                                {{ $asset->asset_tag }} - {{ $asset->model->asset_model ?? 'No Model' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Pilih asset jika aktivitas berkaitan dengan asset tertentu</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="related_ticket_id">Ticket Terkait (Opsional)</label>
                                    <select class="form-control select2" id="related_ticket_id" name="related_ticket_id">
                                        <option value="">Pilih Ticket (jika ada)</option>
                                        @foreach($tickets as $ticket)
                                            <option value="{{ $ticket->id }}" 
                                                {{ old('related_ticket_id', $dailyActivity->related_ticket_id) == $ticket->id ? 'selected' : '' }}>
                                                #{{ $ticket->id }} - {{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Pilih ticket jika aktivitas berkaitan dengan penanganan ticket</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Catatan Tambahan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="500">{{ old('notes', $dailyActivity->notes) }}</textarea>
                            <small class="form-text text-muted">Catatan atau informasi tambahan (maksimal 500 karakter)</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_priority" name="is_priority" value="1"
                                       {{ old('is_priority', $dailyActivity->is_priority) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_priority">
                                    Aktivitas Prioritas
                                </label>
                                <small class="form-text text-muted">Tandai jika ini adalah aktivitas dengan prioritas tinggi</small>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Aktivitas
                            </button>
                            <a href="{{ route('daily-activities.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih...',
        allowClear: true
    });

    // Time validation
    $('#start_time, #end_time').on('change', function() {
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();
        
        if (startTime && endTime && startTime >= endTime) {
            alert('Waktu selesai harus lebih besar dari waktu mulai');
            $('#end_time').val('');
        }
    });

    // Character counter for textarea
    $('#description, #notes').on('input', function() {
        var maxLength = $(this).attr('maxlength');
        var currentLength = $(this).val().length;
        var remaining = maxLength - currentLength;
        
        // Find or create counter element
        var counterId = this.id + '-counter';
        var counter = $('#' + counterId);
        if (counter.length === 0) {
            counter = $('<small class="form-text text-muted" id="' + counterId + '"></small>');
            $(this).after(counter);
        }
        
        counter.text(remaining + ' karakter tersisa');
        
        if (remaining < 50) {
            counter.addClass('text-warning');
        } else {
            counter.removeClass('text-warning');
        }
        
        if (remaining < 0) {
            counter.addClass('text-danger').removeClass('text-warning');
        } else {
            counter.removeClass('text-danger');
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.select2-container--bootstrap4 .select2-selection {
    height: calc(2.25rem + 2px);
}
</style>
@endpush
@endsection