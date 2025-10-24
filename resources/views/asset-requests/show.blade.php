@extends('layouts.app')

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Asset Request {{ $assetRequest->id ?? $asset_request->id ?? '—' }}</h3>
                    <div class="card-tools">
                        @if (Route::has('asset-requests.index'))
                            <a href="{{ route('asset-requests.index') }}" class="btn btn-sm btn-secondary">Back to requests</a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Requested for asset</dt>
                        <dd class="col-sm-9">
                            @if(optional($assetRequest)->asset)
                                <a href="{{ Route::has('assets.show') ? route('assets.show', $assetRequest->asset) : '#' }}">{{ $assetRequest->asset->asset_tag ?? $assetRequest->asset->id ?? '—' }}</a>
                            @else
                                {{ $assetRequest->asset_tag ?? '—' }}
                            @endif
                        </dd>

                        <dt class="col-sm-3">Requested by</dt>
                        <dd class="col-sm-9">{{ optional($assetRequest->requester)->name ?? optional($assetRequest->user)->name ?? '—' }}</dd>

                        <dt class="col-sm-3">Quantity</dt>
                        <dd class="col-sm-9">{{ $assetRequest->quantity ?? '1' }}</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">{{ $assetRequest->status->name ?? $assetRequest->status ?? '—' }}</dd>

                        <dt class="col-sm-3">Created</dt>
                        <dd class="col-sm-9">{{ optional($assetRequest->created_at)->format('Y-m-d H:i') ?? '—' }}</dd>

                        <dt class="col-sm-3">Updated</dt>
                        <dd class="col-sm-9">{{ optional($assetRequest->updated_at)->format('Y-m-d H:i') ?? '—' }}</dd>

                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $assetRequest->notes ?? '—' }}</dd>
                    </dl>

                    @if(Route::has('asset-requests.edit'))
                        <a href="{{ route('asset-requests.edit', $assetRequest) }}" class="btn btn-primary">Edit Request</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
