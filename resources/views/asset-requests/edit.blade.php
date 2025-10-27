@extends('layouts.app')

@section('main-content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Asset Request #{{ $assetRequest->id }}</h3>
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

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('asset-requests.update', $assetRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="asset_type_id">Tipe Asset <span class="text-danger">*</span></label>
                                    <select class="form-control @error('asset_type_id') is-invalid @enderror" 
                                            id="asset_type_id" name="asset_type_id" required>
                                        <option value="">Pilih Tipe Asset</option>
                                        @foreach($assetTypes as $type)
                                            <option value="{{ $type->id }}" 
                                                    {{ old('asset_type_id', $assetRequest->asset_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('asset_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" class="form-control" id="status" value="{{ ucfirst($assetRequest->status) }}" disabled>
                                    <small class="form-text text-muted">Status hanya dapat diubah melalui proses approval/rejection</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="justification">Justification / Alasan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('justification') is-invalid @enderror" 
                                      id="justification" name="justification" rows="6" required>{{ old('justification', $assetRequest->justification) }}</textarea>
                            <small class="form-text text-muted">
                                Jelaskan secara detail:
                                <ul>
                                    <li>Mengapa asset ini dibutuhkan?</li>
                                    <li>Untuk keperluan apa asset akan digunakan?</li>
                                    <li>Dampak jika tidak mendapatkan asset ini</li>
                                </ul>
                            </small>
                            @error('justification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="requested_by">Diminta Oleh</label>
                                    <input type="text" class="form-control" value="{{ $assetRequest->requestedBy->name ?? 'N/A' }}" disabled>
                                    <small class="form-text text-muted">Tidak dapat diubah</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="created_at">Tanggal Dibuat</label>
                                    <input type="text" class="form-control" value="{{ $assetRequest->created_at->format('d M Y H:i') }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="updated_at">Terakhir Diperbarui</label>
                                    <input type="text" class="form-control" value="{{ $assetRequest->updated_at->format('d M Y H:i') }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Request
                            </button>
                            <a href="{{ route('asset-requests.show', $assetRequest->id) }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
