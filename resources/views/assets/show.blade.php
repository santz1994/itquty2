@extends('layouts.app')

@section('main-content')

{{-- All styles from centralized CSS: public/css/ui-enhancements.css --}}

@php $pageTitle = $pageTitle ?? ('Asset Details - ' . ($asset->asset_tag ?? '')); @endphp

{{-- Page Header --}}
@include('components.page-header', [
    'title' => 'Asset Details',
    'subtitle' => $asset->asset_tag ?? 'Asset Information',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => $asset->asset_tag ?? 'Details']
    ]
])

<div class="container-fluid">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-9">
            {{-- Asset Information Card --}}
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-laptop"></i> {{ $pageTitle }}
                        @if($asset->status)
                            <span class="label label-{{ $asset->status->color ?? 'default' }}" style="margin-left: 10px;">
                                {{ $asset->status->status }}
                            </span>
                        @endif
                    </h3>
                    <div class="box-tools pull-right">
                        <a href="{{ route('assets.index') }}" class="btn btn-default btn-xs">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ url('assets/' . $asset->id . '/print') }}" class="btn btn-info btn-xs" target="_blank">
                            <i class="fa fa-print"></i> Print
                        </a>
                        <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-primary btn-xs">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
            
                <div class="box-body">
                    {{-- Nav tabs --}}
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#basic-info" aria-controls="basic-info" role="tab" data-toggle="tab">
                                <i class="fa fa-info-circle"></i> Basic Info
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#specifications" aria-controls="specifications" role="tab" data-toggle="tab">
                                <i class="fa fa-cogs"></i> Specifications
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#network-info" aria-controls="network-info" role="tab" data-toggle="tab">
                                <i class="fa fa-network-wired"></i> Network
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#tickets" aria-controls="tickets" role="tab" data-toggle="tab">
                                <i class="fa fa-ticket"></i> Tickets 
                                @if($recentIssues->count() > 0)
                                    <span class="badge bg-red">{{ $recentIssues->count() }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>

                    {{-- Tab panes --}}
                    <div class="tab-content" style="padding-top: 20px;">
                        {{-- Basic Information Tab --}}
                        <div role="tabpanel" class="tab-pane active" id="basic-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><i class="fa fa-info-circle text-primary"></i> Basic Information</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tr>
                                                <th style="width: 150px;">Asset Tag:</th>
                                                <td><strong>{{ $asset->asset_tag }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Model:</th>
                                                <td>{{ optional($asset->model)->asset_model ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Serial Number:</th>
                                                <td>{{ $asset->serial_number ?: 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status:</th>
                                                <td>
                                                    <span class="label label-{{ optional($asset->status)->color ?? 'default' }}">
                                                        {{ optional($asset->status)->status ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @if(optional($asset->location)->location_name)
                                                <tr>
                                                    <th>Location:</th>
                                                    <td>
                                                        <i class="fa fa-map-marker"></i> {{ $asset->location->location_name }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(optional($asset->division)->division_name)
                                                <tr>
                                                    <th>Division:</th>
                                                    <td>
                                                        <i class="fa fa-building"></i> {{ $asset->division->division_name }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h4><i class="fa fa-calendar text-info"></i> Purchase & Warranty</h4>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            @if($asset->purchase_date)
                                                <tr>
                                                    <th style="width: 150px;">Purchase Date:</th>
                                                    <td>{{ $asset->purchase_date->format('d F Y') }}</td>
                                                </tr>
                                            @endif
                                            
                                            @if(optional($asset->supplier)->name)
                                                <tr>
                                                    <th>Supplier:</th>
                                                    <td>{{ $asset->supplier->name }}</td>
                                                </tr>
                                            @endif
                                            
                                            @if($asset->warranty_months)
                                                <tr>
                                                    <th>Warranty Months:</th>
                                                    <td>{{ $asset->warranty_months }} months</td>
                                                </tr>
                                                @php
                                                    $warrantyEnd = $asset->purchase_date ? $asset->purchase_date->addMonths($asset->warranty_months) : null;
                                                    $isWarrantyActive = $warrantyEnd && $warrantyEnd->isFuture();
                                                @endphp
                                                @if($warrantyEnd)
                                                    <tr>
                                                        <th>Warranty Expires:</th>
                                                        <td>
                                                            {{ $warrantyEnd->format('d F Y') }}
                                                            @if($isWarrantyActive)
                                                                <span class="label label-success">Active</span>
                                                            @else
                                                                <span class="label label-danger">Expired</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>

                            @if($asset->notes)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4><i class="fa fa-sticky-note text-warning"></i> Notes</h4>
                                        <div class="well well-sm">
                                            {!! nl2br(e($asset->notes)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Specifications Tab --}}
                        <div role="tabpanel" class="tab-pane" id="specifications">
                            @if($asset->pcspec)
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4><i class="fa fa-microchip text-primary"></i> Hardware Specifications</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th style="width: 150px;">Processor:</th>
                                                    <td>{{ $asset->pcspec->processor ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>RAM:</th>
                                                    <td>{{ $asset->pcspec->ram ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Storage:</th>
                                                    <td>{{ $asset->pcspec->storage ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Graphics:</th>
                                                    <td>{{ $asset->pcspec->graphics ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h4><i class="fa fa-desktop text-info"></i> Display & Software</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th style="width: 150px;">Display:</th>
                                                    <td>{{ $asset->pcspec->display ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Operating System:</th>
                                                    <td>{{ $asset->pcspec->os ?? 'N/A' }}</td>
                                                </tr>
                                                @if($asset->pcspec->additional_specs)
                                                    <tr>
                                                        <th>Additional:</th>
                                                        <td>{{ $asset->pcspec->additional_specs }}</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No specifications available for this asset.
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="alert-link">Add specifications</a>
                                </div>
                            @endif
                        </div>

                        {{-- Network Information Tab --}}
                        <div role="tabpanel" class="tab-pane" id="network-info">
                            @if($asset->ip_address || $asset->mac_address)
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4><i class="fa fa-network-wired text-success"></i> Network Information</h4>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                @if($asset->ip_address)
                                                    <tr>
                                                        <th style="width: 150px;">IP Address:</th>
                                                        <td><code>{{ $asset->ip_address }}</code></td>
                                                    </tr>
                                                @endif
                                                @if($asset->mac_address)
                                                    <tr>
                                                        <th>MAC Address:</th>
                                                        <td><code>{{ $asset->mac_address }}</code></td>
                                                    </tr>
                                                @endif
                                                @if($asset->computer_name)
                                                    <tr>
                                                        <th>Computer Name:</th>
                                                        <td><code>{{ $asset->computer_name }}</code></td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> No network information available for this asset.
                                </div>
                            @endif
                        </div>

                        {{-- Tickets Tab --}}
                        <div role="tabpanel" class="tab-pane" id="tickets">
                            @if($ticketHistory->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="bg-light-blue">
                                                <th>Ticket Code</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Priority</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($ticketHistory->sortByDesc('created_at') as $ticket)
                                                <tr>
                                                    <td><strong>{{ $ticket->ticket_code }}</strong></td>
                                                    <td>{{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}</td>
                                                    <td>
                                                        <span class="label label-{{ optional($ticket->ticket_status)->color ?? 'default' }}">
                                                            {{ optional($ticket->ticket_status)->status ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-{{ optional($ticket->ticket_priority)->color ?? 'default' }}">
                                                            {{ optional($ticket->ticket_priority)->priority ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                                    <td>
                                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-xs btn-info">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="fa fa-check-circle"></i> No tickets found for this asset. Great job!
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-md-3">
        {{-- Quick Actions --}}
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="box-body">
                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-primary btn-block margin-bottom">
                    <i class="fa fa-edit"></i> Edit Asset
                </a>
                <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="btn btn-success btn-block margin-bottom">
                    <i class="fa fa-plus"></i> Create Ticket
                </a>
                <a href="{{ url('assets/' . $asset->id . '/print') }}" class="btn btn-info btn-block margin-bottom" target="_blank">
                    <i class="fa fa-print"></i> Print Label
                </a>
                @if($asset->qr_code)
                    <button class="btn btn-default btn-block margin-bottom" onclick="showQRCode()">
                        <i class="fa fa-qrcode"></i> View QR Code
                    </button>
                @endif
                <a href="{{ route('assets.index') }}" class="btn btn-default btn-block">
                    <i class="fa fa-arrow-left"></i> Back to Assets
                </a>
            </div>
        </div>

        {{-- Asset Statistics --}}
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Statistics</h3>
            </div>
            <div class="box-body">
                {{-- Total Tickets --}}
                <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-ticket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Tickets</span>
                        <span class="info-box-number">{{ $ticketHistory->count() }}</span>
                    </div>
                </div>

                {{-- Recent Issues --}}
                <div class="info-box bg-{{ $recentIssues->count() > 0 ? 'red' : 'green' }}">
                    <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recent Issues (30 days)</span>
                        <span class="info-box-number">{{ $recentIssues->count() }}</span>
                    </div>
                </div>

                {{-- Asset Age --}}
                @if($asset->purchase_date)
                    @php
                        $assetAge = $asset->purchase_date->diff(now());
                        $years = $assetAge->y;
                        $months = $assetAge->m;
                    @endphp
                    <div class="info-box bg-blue">
                        <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Asset Age</span>
                            <span class="info-box-number">{{ $years }}y {{ $months }}m</span>
                        </div>
                    </div>
                @endif

                {{-- Warranty Status --}}
                @if($asset->warranty_months && $asset->purchase_date)
                    @php
                        $warrantyEnd = $asset->purchase_date->copy()->addMonths($asset->warranty_months);
                        $isWarrantyActive = $warrantyEnd->isFuture();
                    @endphp
                    <div class="info-box bg-{{ $isWarrantyActive ? 'green' : 'red' }}">
                        <span class="info-box-icon"><i class="fa fa-shield"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Warranty</span>
                            <span class="info-box-number">{{ $isWarrantyActive ? 'Active' : 'Expired' }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Related Links --}}
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-link"></i> Related Links</h3>
            </div>
            <div class="box-body">
                <ul class="list-unstyled">
                    @if($asset->model)
                        <li class="margin-bottom">
                            <i class="fa fa-laptop text-blue"></i>
                            <a href="{{ route('models.show', $asset->model->id) }}">View Model Details</a>
                        </li>
                    @endif
                    @if($asset->location)
                        <li class="margin-bottom">
                            <i class="fa fa-map-marker text-green"></i>
                            <a href="{{ route('locations.show', $asset->location->id) }}">View Location</a>
                        </li>
                    @endif
                    @if($asset->division)
                        <li class="margin-bottom">
                            <i class="fa fa-building text-orange"></i>
                            <a href="{{ route('divisions.show', $asset->division->id) }}">View Division</a>
                        </li>
                    @endif
                    @if($asset->supplier)
                        <li class="margin-bottom">
                            <i class="fa fa-truck text-purple"></i>
                            <a href="{{ route('suppliers.show', $asset->supplier->id) }}">View Supplier</a>
                        </li>
                    @endif
                    <li class="margin-bottom">
                        <i class="fa fa-history text-gray"></i>
                        <a href="{{ route('assets.history', $asset->id) }}">View Asset History</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Information Box --}}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-info-circle"></i> Information</h3>
            </div>
            <div class="box-body">
                <p><strong>About This Asset:</strong></p>
                <ul class="list-unstyled">
                    <li><i class="fa fa-check text-green"></i> Asset details and specifications</li>
                    <li><i class="fa fa-check text-green"></i> Maintenance and ticket history</li>
                    <li><i class="fa fa-check text-green"></i> Network information</li>
                    <li><i class="fa fa-check text-green"></i> Warranty tracking</li>
                </ul>
                <hr>
                <p class="text-muted small">
                    <i class="fa fa-lightbulb-o"></i> <strong>Tip:</strong> Use the tabs above to navigate between different asset information sections.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- QR Code Modal --}}
@if($asset->qr_code)
    <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                    <h4 class="modal-title">QR Code - {{ $asset->asset_tag }}</h4>
                </div>
                <div class="modal-body text-center">
                    <div id="qrcode"></div>
                    <p class="text-muted">{{ $asset->qr_code }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')
@if($asset->qr_code)
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        function showQRCode() {
            $('#qrCodeModal').modal('show');
            const qrCodeDiv = document.getElementById('qrcode');
            qrCodeDiv.innerHTML = '';
            QRCode.toCanvas(qrCodeDiv, '{{ $asset->qr_code }}', { width: 200, height: 200 }, function (error) { if (error) console.error(error); });
        }
    </script>
@endif
@endsection
