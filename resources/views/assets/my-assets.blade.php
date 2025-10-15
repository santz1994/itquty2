@extends('layouts.app')

@section('main-content')
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-briefcase"></i> My Assets
                </h3>
                <div class="box-tools pull-right">
                    <span class="label label-info">{{ $assets->count() }} assets</span>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                @if($assets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="myAssetsTable">
                        <thead>
                            <tr>
                                <th>Asset Tag</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Model</th>
                                <th>Serial Number</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Assigned Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assets as $asset)
                            <tr>
                                <td>
                                    <strong>{{ $asset->asset_tag }}</strong>
                                    @if($asset->qr_code)
                                    <br><small class="text-muted"><i class="fa fa-qrcode"></i> QR Available</small>
                                    @endif
                                </td>
                                <td>{{ $asset->name }}</td>
                                <td>
                                    @if($asset->assetType)
                                    <span class="label label-default">{{ $asset->assetType->name }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->model)
                                    {{ $asset->model->name }}
                                    @if($asset->model->manufacturer)
                                    <br><small class="text-muted">{{ $asset->model->manufacturer->name }}</small>
                                    @endif
                                    @endif
                                </td>
                                <td>{{ $asset->serial ?? '-' }}</td>
                                <td>
                                    @if($asset->location)
                                    <i class="fa fa-map-marker"></i> {{ $asset->location->name }}
                                    @endif
                                </td>
                                <td>
                                    @if($asset->status)
                                    <span class="label" style="background-color: {{ $asset->status->color ?? '#999' }}">
                                        {{ $asset->status->name }}
                                    </span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->assigned_at)
                                    {{ $asset->assigned_at->format('d M Y') }}
                                    @else
                                    <small class="text-muted">N/A</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('/assets/' . $asset->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if($asset->qr_code)
                                    <a href="{{ url('/assets/' . $asset->id . '/qr-code') }}" class="btn btn-sm btn-default" title="View QR Code" target="_blank">
                                        <i class="fa fa-qrcode"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> You don't have any assets assigned to you at the moment.
                </div>
                @endif
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#myAssetsTable').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[7, "desc"]] // Sort by assigned date descending
    });
});
</script>
@endsection
