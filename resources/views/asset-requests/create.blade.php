@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Ajukan Permintaan Asset</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('asset-requests.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="asset_type_id">Tipe Asset <span class="text-danger">*</span></label>
                                    <select class="form-control" id="asset_type_id" name="asset_type_id" required>
                                        <option value="">Pilih Tipe Asset</option>
                                        @foreach($assetTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('asset_type_id') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">Prioritas <span class="text-danger">*</span></label>
                                    <select class="form-control" id="priority" name="priority" required>
                                        <option value="">Pilih Prioritas</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority }}" 
                                                    {{ old('priority') == $priority ? 'selected' : '' }}>
                                                {{ ucfirst($priority) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title">Judul Permintaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="{{ old('title') }}" required maxlength="255">
                            <small class="form-text text-muted">Berikan judul yang jelas dan deskriptif</small>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi Kebutuhan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            <small class="form-text text-muted">
                                Jelaskan secara detail:
                                <ul>
                                    <li>Mengapa asset ini dibutuhkan?</li>
                                    <li>Untuk keperluan apa asset akan digunakan?</li>
                                    <li>Spesifikasi khusus yang dibutuhkan (jika ada)</li>
                                    <li>Dampak jika tidak mendapatkan asset ini</li>
                                </ul>
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="requested_quantity">Jumlah yang Diminta <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="requested_quantity" name="requested_quantity" 
                                           value="{{ old('requested_quantity', 1) }}" min="1" max="99" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estimated_cost">Perkiraan Biaya (Rp)</label>
                                    <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" 
                                           value="{{ old('estimated_cost') }}" min="0" step="1000">
                                    <small class="form-text text-muted">Jika Anda mengetahui perkiraan biaya (opsional)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="needed_date">Tanggal Dibutuhkan <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="needed_date" name="needed_date" 
                                           value="{{ old('needed_date') }}" required>
                                    <small class="form-text text-muted">Kapan asset ini dibutuhkan?</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usage_duration">Durasi Penggunaan</label>
                                    <select class="form-control" id="usage_duration" name="usage_duration">
                                        <option value="">Pilih Durasi</option>
                                        <option value="temporary" {{ old('usage_duration') == 'temporary' ? 'selected' : '' }}>
                                            Sementara (< 3 bulan)
                                        </option>
                                        <option value="medium_term" {{ old('usage_duration') == 'medium_term' ? 'selected' : '' }}>
                                            Menengah (3-12 bulan)
                                        </option>
                                        <option value="permanent" {{ old('usage_duration') == 'permanent' ? 'selected' : '' }}>
                                            Permanen (> 12 bulan)
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="business_justification">Justifikasi Bisnis <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="business_justification" name="business_justification" 
                                      rows="4" required>{{ old('business_justification') }}</textarea>
                            <small class="form-text text-muted">
                                Jelaskan bagaimana asset ini akan membantu pekerjaan/produktivitas Anda
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="alternative_solutions">Alternatif Solusi yang Sudah Dicoba</label>
                            <textarea class="form-control" id="alternative_solutions" name="alternative_solutions" 
                                      rows="3">{{ old('alternative_solutions') }}</textarea>
                            <small class="form-text text-muted">
                                Apakah ada cara lain yang sudah Anda coba sebelum mengajukan permintaan asset ini?
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="preferred_specifications">Spesifikasi yang Diinginkan</label>
                            <textarea class="form-control" id="preferred_specifications" name="preferred_specifications" 
                                      rows="3">{{ old('preferred_specifications') }}</textarea>
                            <small class="form-text text-muted">
                                Sebutkan spesifikasi teknis khusus yang dibutuhkan (merk, model, fitur, dll)
                            </small>
                        </div>

                        <!-- Additional Information -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>Informasi Tambahan</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="supervisor_approval" 
                                               name="supervisor_approval" value="1" {{ old('supervisor_approval') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="supervisor_approval">
                                            Permintaan ini sudah mendapat persetujuan dari supervisor langsung
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="budget_approved" 
                                               name="budget_approved" value="1" {{ old('budget_approved') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="budget_approved">
                                            Budget untuk asset ini sudah tersedia/disetujui
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="replacement_asset" 
                                               name="replacement_asset" value="1" {{ old('replacement_asset') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="replacement_asset">
                                            Ini adalah penggantian asset yang rusak/tidak bisa digunakan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Ajukan Permintaan
                            </button>
                            <a href="{{ route('asset-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set needed date based on priority
    const prioritySelect = document.getElementById('priority');
    const neededDateInput = document.getElementById('needed_date');
    
    prioritySelect.addEventListener('change', function() {
        const priority = this.value;
        const today = new Date();
        let days = 7; // default
        
        switch(priority) {
            case 'urgent':
                days = 1;
                break;
            case 'high':
                days = 3;
                break;
            case 'medium':
                days = 7;
                break;
            case 'low':
                days = 14;
                break;
        }
        
        today.setDate(today.getDate() + days);
        neededDateInput.value = today.toISOString().slice(0, 10);
    });
    
    // Format estimated cost input
    const estimatedCostInput = document.getElementById('estimated_cost');
    estimatedCostInput.addEventListener('input', function() {
        // Remove non-numeric characters except dots
        let value = this.value.replace(/[^\d]/g, '');
        
        // Format with thousand separators
        if (value) {
            this.value = parseInt(value).toLocaleString('id-ID');
        }
    });
});
</script>
@endsection