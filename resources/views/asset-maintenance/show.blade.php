@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Asset Maintenance History - {{ $asset->name }}</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('asset-maintenance.index') }}" class="btn btn-sm btn-default">
                            <i class="fa fa-arrow-left"></i> Back to Maintenance Dashboard
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h4>Asset Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Asset Tag:</th>
                                    <td>{{ $asset->asset_tag }}</td>
                                </tr>
                                <tr>
                                    <th>Model:</th>
                                    <td>{{ $asset->model->asset_model ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Serial Number:</th>
                                    <td>{{ $asset->serial_number }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>{{ $asset->status->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-8">
                            <h4>Maintenance History</h4>
                            @if($history->isEmpty())
                                <p class="text-muted">No maintenance records found for this asset.</p>
                            @else
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Ticket</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($history as $record)
                                        <tr>
                                            <td>{{ $record->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('tickets.show', $record->id) }}">
                                                    {{ $record->ticket_code }}
                                                </a>
                                            </td>
                                            <td>{{ \Illuminate\Support\Str::limit($record->description, 50) }}</td>
                                            <td>
                                                <span class="label label-{{ $record->ticket_status->color ?? 'default' }}">
                                                    {{ $record->ticket_status->name ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="label label-{{ $record->ticket_priority->color ?? 'default' }}">
                                                    {{ $record->ticket_priority->name ?? 'Unknown' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
