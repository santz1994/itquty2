@extends('layouts.app')

@section('main-content')
@php $pageTitle = $pageTitle ?? ('Asset Details - ' . ($asset->asset_tag ?? '')); @endphp
<div class="row">
    <div class="col-md-8">
        <!-- Asset Information -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $pageTitle }}</h3>
                <div class="box-tools pull-right">
                    <a href="{{ route('assets.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Assets
                    </a>
                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </div>
            </div>
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4><i class="fa fa-info-circle text-primary"></i> Basic Information</h4>
                        
                        <table class="table table-striped">
                            <tr>
                                <th>Asset Tag:</th>
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
                                    <span class="label label-default">{{ optional($asset->status)->name ?? 'Unknown' }}</span>
                                </td>
                            </tr>
                            @if(optional($asset->location)->location_name)
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $asset->location->location_name }}</td>
                                </tr>
                            @endif
                            @if(optional($asset->division)->division_name)
                                <tr>
                                    <th>Division:</th>
                                    <td>{{ $asset->division->division_name }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h4><i class="fa fa-calendar text-info"></i> Purchase & Warranty</h4>
                        
                        <table class="table table-striped">
                            @if($asset->purchase_date)
                                <tr>
                                    <th>Purchase Date:</th>
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
                            @endif
                        </table>
                    </div>
                </div>

                @if($asset->ip_address || $asset->mac_address)
                    <div class="row">
                        <div class="col-md-12">
                            <h4><i class="fa fa-network-wired text-success"></i> Network Info</h4>
                            <table class="table table-striped">
                                @if($asset->ip_address)
                                    <tr>
                                        <th>IP Address:</th>
                                        <td><code>{{ $asset->ip_address }}</code></td>
                                    </tr>
                                @endif
                                @if($asset->mac_address)
                                    <tr>
                                        <th>MAC Address:</th>
                                        <td><code>{{ $asset->mac_address }}</code></td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @endif

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
        </div>
        
        <!-- Recent Issues (Last 30 Days) -->
        @if(isset($recentIssues) && $recentIssues->count() > 0)
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Recent Issues (Last 30 Days)</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ticket Code</th>
                                    <th>Title</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentIssues as $ticket)
                                    <tr>
                                        <td><strong>{{ $ticket->ticket_code }}</strong></td>
                                        <td>{{ $ticket->title }}</td>
                                        <td>
                                            <span class="label label-{{ $ticket->ticket_priority->id == 1 ? 'danger' : ($ticket->ticket_priority->id == 2 ? 'warning' : 'info') }}">
                                                {{ $ticket->ticket_priority->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="label label-primary">{{ optional($ticket->ticket_status)->name ?? 'Unknown' }}</span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('d M Y') }}</td>
                                        <td><a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Ticket History (if available) -->
        @if(isset($ticketHistory) && $ticketHistory->count() > 0)
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-history"></i> Complete Ticket History</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ticket Code</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ticketHistory as $ticket)
                                    <tr>
                                        <td><strong>{{ $ticket->ticket_code }}</strong></td>
                                        <td>{{ optional($ticket->ticket_type)->name ?? 'N/A' }}</td>
                                        <td>{{ $ticket->title }}</td>
                                        <td>
                                            <span class="label label-{{ optional($ticket->ticket_priority)->id == 1 ? 'danger' : (optional($ticket->ticket_priority)->id == 2 ? 'warning' : 'info') }}">
                                                {{ optional($ticket->ticket_priority)->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="label label-primary">{{ optional($ticket->ticket_status)->name ?? 'Unknown' }}</span>
                                        </td>
                                        <td>{{ optional($ticket->user)->name ?? 'Unassigned' }}</td>
                                        <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                                        <td><a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Sidebar -->
    <div class="col-md-4">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="box-body">
                <div class="btn-group-vertical btn-block">
                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit Asset</a>
                    <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="btn btn-success"><i class="fa fa-plus"></i> Open Ticket</a>
                    @if($asset->qr_code)
                        <button class="btn btn-info" onclick="showQRCode()"><i class="fa fa-qrcode"></i> View QR Code</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-info-circle"></i> Asset Status</h3>
            </div>
            <div class="box-body">
                <div class="alert alert-info">{{ optional($asset->status)->name ?? 'Unknown' }}</div>
            </div>
        </div>

        @if($asset->warranty_months)
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Warranty</h3>
                </div>
                <div class="box-body">
                    <p>{{ $asset->warranty_months }} months</p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- QR Code Modal -->
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
