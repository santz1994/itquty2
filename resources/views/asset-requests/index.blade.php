@extends('layouts.app')

@section('main-content')

{{-- Page Header --}}
@include('components.page-header', [
    'title' => 'Asset Requests',
    'subtitle' => 'Manage asset requests and approvals',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Asset Requests']
    ],
    'actions' => '<a href="'.route('asset-requests.create').'" class="btn btn-primary">
        <i class="fa fa-plus"></i> Create New Request
    </a>'
])

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            
            {{-- Filters Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-filter"></i> Filters
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('asset-requests.index') }}" id="filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">All Status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="asset_type">Asset Type</label>
                                    <select name="asset_type" id="asset_type" class="form-control">
                                        <option value="">All Types</option>
                                        @foreach($assetTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('asset_type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select name="priority" id="priority" class="form-control">
                                        <option value="">All Priorities</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                                {{ ucfirst($priority) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('asset-requests.index') }}" class="btn btn-secondary">
                                            <i class="fa fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Requests Table --}}
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-list"></i> Asset Requests
                    <span class="badge badge-primary float-right">{{ $requests->total() }} Total</span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-enhanced table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="sortable" data-column="id">ID</th>
                                    <th class="sortable" data-column="title">Title</th>
                                    <th class="sortable" data-column="asset_type">Asset Type</th>
                                    <th class="sortable" data-column="requested_by">Requested By</th>
                                    <th class="sortable" data-column="priority">Priority</th>
                                    <th class="sortable" data-column="status">Status</th>
                                    <th class="sortable" data-column="needed_date">Needed Date</th>
                                    <th class="sortable" data-column="created_at">Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>#{{ $request->id }}</td>
                                        <td>
                                            <strong>{{ $request->title }}</strong>
                                            @if($request->requested_quantity > 1)
                                                <br><small class="text-muted">Qty: {{ $request->requested_quantity }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->assetType)
                                                <span class="badge badge-info">{{ $request->assetType->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->requestedBy)
                                                {{ $request->requestedBy->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $priorityColors = [
                                                    'low' => 'secondary',
                                                    'medium' => 'info',
                                                    'high' => 'warning',
                                                    'urgent' => 'danger'
                                                ];
                                                $color = $priorityColors[$request->priority] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">{{ ucfirst($request->priority) }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger',
                                                    'fulfilled' => 'primary'
                                                ];
                                                $statusColor = $statusColors[$request->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $statusColor }}">{{ ucfirst($request->status) }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($request->needed_date)->format('d M Y') }}</td>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('asset-requests.show', $request->id) }}" 
                                                   class="btn btn-sm btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                
                                                @can('update', $request)
                                                    <a href="{{ route('asset-requests.edit', $request->id) }}" 
                                                       class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @if(Auth::user()->hasRole(['admin', 'super-admin']) && $request->status === 'pending')
                                                    <form action="{{ route('asset-requests.approve', $request->id) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" 
                                                                title="Approve"
                                                                onclick="return confirm('Approve this request?')">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('asset-requests.reject', $request->id) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                                title="Reject"
                                                                onclick="return confirm('Reject this request?')">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">
                                            <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                            No asset requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $requests->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Loading Overlay --}}
@include('components.loading-overlay')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit filters
    $('#status, #asset_type, #priority').on('change', function() {
        showLoading('Filtering requests...');
        $('#filter-form').submit();
    });
});
</script>
@endpush
