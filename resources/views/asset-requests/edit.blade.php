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

                        @include('asset-requests._form')

                        {{-- Read-only meta fields --}}
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <input type="text" class="form-control" id="status" value="{{ ucfirst($assetRequest->status) }}" disabled>
                                    <small class="form-text text-muted">Status hanya dapat diubah melalui proses approval/rejection</small>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                    <input type="text" class="form-control" value="{{ optional($assetRequest->created_at)->format('d M Y H:i') }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="updated_at">Terakhir Diperbarui</label>
                                    <input type="text" class="form-control" value="{{ optional($assetRequest->updated_at)->format('d M Y H:i') }}" disabled>
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
