@extends('layouts.app')

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Movements for asset: {{ $asset->asset_tag ?? '—' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-secondary">Back to asset</a>
                    </div>
                </div>

                <div class="card-body">
                    @if($movements->isEmpty())
                        <p>No movements found for this asset.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Moved By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $m)
                                    <tr>
                                        <td>{{ optional($m->created_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ optional($m->from_location)->location_name ?? '—' }}</td>
                                        <td>{{ optional($m->to_location)->location_name ?? '—' }}</td>
                                        <td>{{ optional($m->moved_by)->name ?? (optional($m->user)->name ?? '—') }}</td>
                                        <td>{{ $m->notes ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
