@extends('layouts.app')

@section('main-content')

{{-- All styles moved to public/css/ui-enhancements.css for better performance and maintainability --}}

@include('components.page-header', [
    'title' => 'Edit Ticket #' . $ticket->ticket_code,
    'subtitle' => 'Update ticket details',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Tickets', 'url' => route('tickets.index')],
        ['label' => 'Edit #' . $ticket->ticket_code]
    ],
    'actions' => '<a href="'.route('tickets.show', $ticket).'" class="btn btn-default">
        <i class="fa fa-eye"></i> View Ticket
    </a>
    <a href="'.route('tickets.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
])

<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Edit Ticket #{{ $ticket->ticket_code }}</h3>
            </div>
            <div class="box-body">
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fa fa-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <i class="icon fa fa-ban"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- Ticket Metadata --}}
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> <strong>Ticket Info:</strong>
                    Created: {{ $ticket->created_at ? $ticket->created_at->format('d M Y H:i') : 'N/A' }} by {{ $ticket->user->name ?? 'N/A' }} |
                    Last Updated: {{ $ticket->updated_at ? $ticket->updated_at->format('d M Y H:i') : 'N/A' }}
                    @if($ticket->resolved_at)
                        | <span class="text-success"><i class="fa fa-check-circle"></i> Resolved: {{ $ticket->resolved_at->format('d M Y H:i') }}</span>
                    @endif
                </div>

                <form method="POST" action="{{ route('tickets.update', $ticket) }}" id="ticket-edit-form">
                    @csrf
                    @method('PUT')
                    
                    {{-- SECTION 1: Basic Information --}}
                    <fieldset>
                        <legend><i class="fa fa-info-circle"></i> Basic Information</legend>

                        <div class="form-group">
                            <label for="subject">Subject <span class="text-red">*</span></label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   name="subject" 
                                   id="subject"
                                   value="{{ old('subject', $ticket->subject) }}" 
                                   required maxlength="255">
                            <small class="text-muted">Brief summary of the issue (max 255 characters)</small>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span class="text-red">*</span></label>
                            <span id="char-counter">0 / 10 characters (minimum 10)</span>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" 
                                      id="description" 
                                      rows="5" 
                                      required minlength="10">{{ old('description', $ticket->description) }}</textarea>
                            <small class="text-muted">Detailed description of the issue or request (minimum 10 characters)</small>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ticket_type_id">Ticket Type <span class="text-red">*</span></label>
                            <select class="form-control @error('ticket_type_id') is-invalid @enderror" 
                                    name="ticket_type_id" 
                                    id="ticket_type_id" 
                                    required>
                                <option value="">-- Select Ticket Type --</option>
                                @foreach($ticketsTypes as $type)
                                    <option value="{{ $type->id }}" 
                                            {{ old('ticket_type_id', $ticket->ticket_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->type }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Category of request (e.g., Hardware Issue, Software Support, Network Problem)</small>
                            @error('ticket_type_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ticket_priority_id">Priority <span class="text-red">*</span></label>
                            <select class="form-control @error('ticket_priority_id') is-invalid @enderror" 
                                    name="ticket_priority_id" 
                                    id="ticket_priority_id" 
                                    required>
                                <option value="">-- Select Priority --</option>
                                @foreach($ticketsPriorities as $priority)
                                    <option value="{{ $priority->id }}" 
                                            {{ old('ticket_priority_id', $ticket->ticket_priority_id) == $priority->id ? 'selected' : '' }}>
                                        {{ $priority->priority }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Urgency level - affects SLA due date (High = urgent, Medium = normal, Low = can wait)</small>
                            @error('ticket_priority_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="ticket_status_id">Status <span class="text-red">*</span></label>
                            <select class="form-control @error('ticket_status_id') is-invalid @enderror" 
                                    name="ticket_status_id" 
                                    id="ticket_status_id" 
                                    required>
                                <option value="">-- Select Status --</option>
                                @foreach($ticketsStatuses as $status)
                                    <option value="{{ $status->id }}" 
                                            {{ old('ticket_status_id', $ticket->ticket_status_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->status }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Current ticket status</small>
                            @error('ticket_status_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- SECTION 2: Assignment & Location --}}
                    <fieldset>
                        <legend><i class="fa fa-user"></i> Assignment & Location</legend>

                        <div class="form-group">
                            <label for="assigned_to">Assigned To</label>
                            <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                    name="assigned_to" 
                                    id="assigned_to">
                                <option value="">-- Unassigned --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Technician responsible for resolving this ticket</small>
                            @error('assigned_to')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="location_id">Location <span class="text-red">*</span></label>
                            <select class="form-control @error('location_id') is-invalid @enderror" 
                                    name="location_id" 
                                    id="location_id" required>
                                <option value="">-- Select Location --</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" 
                                            {{ old('location_id', $ticket->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->location_name }} - {{ $location->building }}, {{ $location->office }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Physical location where issue is occurring</small>
                            @error('location_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- SECTION 3: Asset Association --}}
                    <fieldset>
                        <legend><i class="fa fa-laptop"></i> Asset Association</legend>

                        <div class="form-group">
                            <label for="asset_id">Related Assets (Optional)</label>
                            <select class="form-control @error('asset_ids') is-invalid @enderror @error('asset_ids.*') is-invalid @enderror" 
                                    name="asset_ids[]" 
                                    id="asset_id" multiple>
                                @php $selectedAssets = old('asset_ids', $ticket->assets->pluck('id')->toArray()); @endphp
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                            {{ in_array($asset->id, $selectedAssets ?? []) ? 'selected' : '' }}>
                                        {{ $asset->model_name ? $asset->model_name : 'Unknown Model' }} ({{ $asset->asset_tag }}) - {{ $asset->location ? $asset->location->location_name : 'No Location' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Select one or more assets related to this ticket (use Ctrl/Cmd + Click for multiple)</small>
                            @error('asset_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('asset_ids.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- Submit Buttons --}}
                    <div class="form-group" style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa fa-save"></i> <b>Update Ticket</b>
                        </button>
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-info btn-lg">
                            <i class="fa fa-eye"></i> View
                        </a>
                        <a href="{{ route('tickets.index') }}" class="btn btn-default btn-lg">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- SIDEBAR: Ticket Information & Help --}}
    <div class="col-md-4">
        {{-- Ticket Details --}}
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-ticket"></i> Ticket Details</h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal" style="margin-bottom: 0;">
                    <dt>Ticket Code:</dt>
                    <dd><strong class="text-primary">{{ $ticket->ticket_code }}</strong></dd>
                    
                    <dt>Created By:</dt>
                    <dd>{{ $ticket->user->name ?? 'N/A' }}</dd>
                    
                    <dt>Created At:</dt>
                    <dd>{{ $ticket->created_at->format('M j, Y H:i') }}</dd>
                    
                    <dt>Last Updated:</dt>
                    <dd>{{ $ticket->updated_at->format('M j, Y H:i') }}</dd>
                    
                    @if($ticket->resolved_at)
                        <dt>Resolved At:</dt>
                        <dd><span class="text-success"><i class="fa fa-check-circle"></i> {{ $ticket->resolved_at->format('M j, Y H:i') }}</span></dd>
                    @endif

                    @if($ticket->sla_due)
                        <dt>SLA Due:</dt>
                        <dd>
                            @php
                                $now = now();
                                $isOverdue = $now->gt($ticket->sla_due);
                                $hoursRemaining = $now->diffInHours($ticket->sla_due, false);
                            @endphp
                            @if($isOverdue)
                                <span class="text-danger"><i class="fa fa-exclamation-triangle"></i> {{ $ticket->sla_due->format('M j, Y H:i') }}</span>
                                <br><small class="text-danger">Overdue by {{ abs($hoursRemaining) }} hours</small>
                            @elseif($hoursRemaining < 4)
                                <span class="text-warning"><i class="fa fa-clock-o"></i> {{ $ticket->sla_due->format('M j, Y H:i') }}</span>
                                <br><small class="text-warning">{{ $hoursRemaining }} hours remaining</small>
                            @else
                                <span class="text-success"><i class="fa fa-check"></i> {{ $ticket->sla_due->format('M j, Y H:i') }}</span>
                                <br><small class="text-success">{{ $hoursRemaining }} hours remaining</small>
                            @endif
                        </dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Current Assets --}}
        @if($ticket->assets->count() > 0)
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-laptop"></i> Current Assets</h3>
                </div>
                <div class="box-body">
                    <ul class="list-unstyled" style="font-size: 12px; margin-bottom: 0;">
                        @foreach($ticket->assets as $asset)
                            <li style="margin-bottom: 8px;">
                                <i class="fa fa-check-circle text-success"></i>
                                <strong>{{ $asset->asset_tag }}</strong><br>
                                <span class="text-muted">{{ $asset->model_name ?? 'Unknown Model' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Help & Tips --}}
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-question-circle"></i> Edit Tips</h3>
            </div>
            <div class="box-body">
                <p><strong>Priority Guidelines:</strong></p>
                <ul class="list-unstyled">
                    <li><span class="badge bg-red">High</span> System down, critical issue</li>
                    <li><span class="badge bg-yellow">Medium</span> Affecting work but not critical</li>
                    <li><span class="badge bg-green">Low</span> Minor issue or request</li>
                </ul>
                
                <hr>
                
                <p><strong>Status Options:</strong></p>
                <ul style="font-size: 12px;">
                    <li><i class="fa fa-circle-o"></i> Open - Just created</li>
                    <li><i class="fa fa-cog"></i> In Progress - Being worked on</li>
                    <li><i class="fa fa-pause"></i> On Hold - Waiting for info</li>
                    <li><i class="fa fa-check"></i> Resolved - Issue fixed</li>
                    <li><i class="fa fa-times"></i> Closed - Completed</li>
                </ul>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="box-body">
                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-info btn-block btn-sm">
                    <i class="fa fa-eye"></i> View Full Ticket
                </a>
                <a href="{{ route('tickets.index') }}" class="btn btn-default btn-block btn-sm">
                    <i class="fa fa-list"></i> Back to All Tickets
                </a>
            </div>
        </div>
    </div>
</div>

@include('components.loading-overlay')

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize Select2 for all dropdowns
    $('#ticket_type_id').select2({ placeholder: 'Select ticket type', allowClear: false });
    $('#ticket_priority_id').select2({ placeholder: 'Select priority', allowClear: false });
    $('#ticket_status_id').select2({ placeholder: 'Select status', allowClear: false });
    $('#assigned_to').select2({ placeholder: 'Select technician (optional)', allowClear: true });
    $('#location_id').select2({ placeholder: 'Select location', allowClear: false });
    
    // Init multi-select for assets with better styling
    $('#asset_id').select2({ 
        placeholder: 'Search and select asset(s)', 
        allowClear: true,
        width: '100%'
    });

    // Character counter for description
    function updateCharCounter() {
        var length = $('#description').val().length;
        var minLength = 10;
        var counter = $('#char-counter');
        
        counter.text(length + ' / ' + minLength + ' characters (minimum ' + minLength + ')');
        
        if (length >= minLength) {
            counter.removeClass('invalid').addClass('valid');
        } else {
            counter.removeClass('valid').addClass('invalid');
        }
    }

    // Update counter on load and on input
    updateCharCounter();
    $('#description').on('input', updateCharCounter);

    // Form submit with loading overlay
    $('#ticket-edit-form').on('submit', function() {
        showLoading('Updating ticket...');
    });

    // Prevent enter key from submitting form
    $(":input").keypress(function(event){
        if (event.which == '10' || event.which == '13') {
            event.preventDefault();
        }
    });
});
</script>
@endpush

@endsection