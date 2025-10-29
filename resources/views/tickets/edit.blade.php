@extends('layouts.app')

@section('main-content')

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
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" id="ticket-edit-form">
                @csrf
                @method('PUT')
                
                <div class="box-body">
                    <!-- Subject Field -->
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" 
                               class="form-control @error('subject') is-invalid @enderror" 
                               name="subject" 
                               id="subject"
                               value="{{ old('subject', $ticket->subject) }}" 
                               required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" 
                                  id="description" 
                                  rows="5" 
                                  required>{{ old('description', $ticket->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Priority Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ticket_priority_id">Priority</label>
                                <select class="form-control @error('ticket_priority_id') is-invalid @enderror" 
                                        name="ticket_priority_id" 
                                        id="ticket_priority_id" 
                                        required>
                                    <option value="">Select Priority</option>
                                    @foreach($ticketsPriorities as $priority)
                                        <option value="{{ $priority->id }}" 
                                                {{ old('ticket_priority_id', $ticket->ticket_priority_id) == $priority->id ? 'selected' : '' }}>
                                            {{ $priority->priority }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ticket_priority_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Type Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ticket_type_id">Type</label>
                                <select class="form-control @error('ticket_type_id') is-invalid @enderror" 
                                        name="ticket_type_id" 
                                        id="ticket_type_id" 
                                        required>
                                    <option value="">Select Type</option>
                                    @foreach($ticketsTypes as $type)
                                        <option value="{{ $type->id }}" 
                                                {{ old('ticket_type_id', $ticket->ticket_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ticket_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Status Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ticket_status_id">Status</label>
                                <select class="form-control @error('ticket_status_id') is-invalid @enderror" 
                                        name="ticket_status_id" 
                                        id="ticket_status_id" 
                                        required>
                                    <option value="">Select Status</option>
                                    @foreach($ticketsStatuses as $status)
                                        <option value="{{ $status->id }}" 
                                                {{ old('ticket_status_id', $ticket->ticket_status_id) == $status->id ? 'selected' : '' }}>
                                            {{ $status->status }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ticket_status_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Assigned To Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="assigned_to">Assigned To</label>
                                <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                        name="assigned_to" 
                                        id="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ old('assigned_to', $ticket->assigned_to) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Location Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location_id">Location</label>
                                <select class="form-control @error('location_id') is-invalid @enderror" 
                                        name="location_id" 
                                        id="location_id">
                                    <option value="">Select Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" 
                                                {{ old('location_id', $ticket->location_id) == $location->id ? 'selected' : '' }}>
                                            {{ $location->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Asset Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="asset_id">Asset</label>
                                <select class="form-control @error('asset_id') is-invalid @enderror" 
                                        name="asset_ids[]" 
                                        id="asset_id" multiple>
                                    <option value="">No Asset</option>
                                    @php $selectedAssets = old('asset_ids', $ticket->assets->pluck('id')->toArray()); @endphp
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}" 
                                                {{ in_array($asset->id, $selectedAssets ?? []) ? 'selected' : '' }}>
                                            {{ $asset->model_name ? $asset->model_name : 'Unknown Model' }} ({{ $asset->asset_tag }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Update Ticket
                    </button>
                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Ticket Information</h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>Ticket Code:</dt>
                    <dd>{{ $ticket->ticket_code }}</dd>
                    
                    <dt>Created By:</dt>
                    <dd>{{ $ticket->user->name }}</dd>
                    
                    <dt>Created At:</dt>
                    <dd>{{ $ticket->created_at->format('M j, Y H:i') }}</dd>
                    
                    <dt>Last Updated:</dt>
                    <dd>{{ $ticket->updated_at->format('M j, Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

@include('components.loading-overlay')

@push('scripts')
<script>
$(document).ready(function() {
    $('#ticket-edit-form').on('submit', function() {
        showLoading('Updating ticket...');
    });
    // Init multi-select for assets
    $('#asset_id').select2({ placeholder: 'Select asset(s)', allowClear: true });
});
</script>
@endpush

@endsection