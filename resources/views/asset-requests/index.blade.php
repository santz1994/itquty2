@extends('layouts.app')

@section('main-content')

{{-- All styles from centralized CSS: public/css/ui-enhancements.css --}}

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

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-circle"></i> <strong>Validation errors:</strong>
            <ul style="margin-bottom: 0; margin-top: 5px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Quick Stats Cards --}}
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua" onclick="filterByStatus('all')" style="cursor: pointer;">
                <div class="inner">
                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                    <p>Total Requests</p>
                </div>
                <div class="icon">
                    <i class="fa fa-inbox"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow" onclick="filterByStatus('pending')" style="cursor: pointer;">
                <div class="inner">
                    <h3>{{ $stats['pending'] ?? 0 }}</h3>
                    <p>Pending Approval</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green" onclick="filterByStatus('approved')" style="cursor: pointer;">
                <div class="inner">
                    <h3>{{ $stats['approved'] ?? 0 }}</h3>
                    <p>Approved</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red" onclick="filterByStatus('rejected')" style="cursor: pointer;">
                <div class="inner">
                    <h3>{{ $stats['rejected'] ?? 0 }}</h3>
                    <p>Rejected</p>
                </div>
                <div class="icon">
                    <i class="fa fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            
            {{-- Enhanced Filters --}}
            <div class="box box-default collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-filter"></i> Advanced Filters</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body filter-bar">
                    <form method="GET" action="{{ route('asset-requests.index') }}" id="filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status"><i class="fa fa-info-circle"></i> Status</label>
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
                                    <label for="asset_type"><i class="fa fa-box"></i> Asset Type</label>
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
                                    <label for="priority"><i class="fa fa-exclamation-triangle"></i> Priority</label>
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
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-search"></i> Apply Filters
                                        </button>
                                        <a href="{{ route('asset-requests.index') }}" class="btn btn-default btn-block" style="margin-top: 5px;">
                                            <i class="fa fa-times"></i> Clear All
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Enhanced Requests Table --}}
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-clipboard-list"></i> Asset Requests</h3>
                    <span class="count-badge">{{ method_exists($requests, 'total') ? $requests->total() : count($requests) }}</span>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="requests-table" class="table table-enhanced table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Request #</th>
                                    <th>Title</th>
                                    <th style="width: 120px;">Asset Type</th>
                                    <th>Requested By</th>
                                    <th style="width: 100px;">Priority</th>
                                    <th style="width: 100px;">Status</th>
                                    <th style="width: 110px;">Needed Date</th>
                                    <th style="width: 100px;">Created</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($requests as $request)
                                    <tr>
                                        <td>#{{ $request->id }}</td>
                                        <td><strong>{{ $request->request_number ?? '-' }}</strong></td>
                                        <td>
                                            <strong>{{ $request->title }}</strong>
                                            @if($request->requested_quantity > 1)
                                                <br><small class="text-muted"><i class="fa fa-box"></i> Qty: {{ $request->requested_quantity }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->assetType)
                                                <span class="label label-info">{{ $request->assetType->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->requestedBy)
                                                <i class="fa fa-user"></i> {{ $request->requestedBy->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->priority === 'urgent')
                                                <span class="label label-danger"><i class="fa fa-bolt"></i> Urgent</span>
                                            @elseif($request->priority === 'high')
                                                <span class="label label-warning"><i class="fa fa-arrow-up"></i> High</span>
                                            @elseif($request->priority === 'medium')
                                                <span class="label label-info"><i class="fa fa-minus"></i> Medium</span>
                                            @else
                                                <span class="label label-default"><i class="fa fa-arrow-down"></i> Low</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->status === 'fulfilled')
                                                <span class="label label-primary"><i class="fa fa-check-double"></i> Fulfilled</span>
                                            @elseif($request->status === 'approved')
                                                <span class="label label-success"><i class="fa fa-check"></i> Approved</span>
                                            @elseif($request->status === 'rejected')
                                                <span class="label label-danger"><i class="fa fa-times"></i> Rejected</span>
                                            @else
                                                <span class="label label-warning"><i class="fa fa-clock"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->needed_date)
                                                {{ \Carbon\Carbon::parse($request->needed_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('asset-requests.show', $request->id) }}" 
                                                   class="btn btn-xs btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if($request->status === 'pending')
                                                    <a href="{{ route('asset-requests.edit', $request->id) }}" 
                                                       class="btn btn-xs btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if(Auth::user()->hasRole(['admin', 'super-admin']) && $request->status === 'pending')
                                                    <form action="{{ route('asset-requests.approve', $request->id) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-success" 
                                                                title="Approve"
                                                                onclick="return confirm('Approve this request?')">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('asset-requests.reject', $request->id) }}" 
                                                          method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-xs btn-danger" 
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
                                        <td colspan="10" class="text-center empty-state">
                                            <i class="fa fa-inbox fa-3x" style="opacity: 0.3; margin-bottom: 15px;"></i>
                                            <p>No asset requests found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    // Move export buttons to header
    table.buttons().container()
        .appendTo($('.box-header .box-title').parent());

    // Clickable stat cards filtering
    window.filterByStatus = function(status) {
        if (status === 'all') {
            table.search('').draw();
        } else {
            table.search(status).draw();
        }
    };

    // Auto-submit filters
    $('#status, #asset_type, #priority').on('change', function() {
        $('#filter-form').submit();
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
