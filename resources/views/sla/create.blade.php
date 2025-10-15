@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-plus-circle"></i> Create SLA Policy
                        </h3>
                        <a href="{{ route('sla.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <form action="{{ route('sla.store') }}" method="POST">
                    @csrf
                    
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5><i class="fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        Policy Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="e.g., Urgent Priority SLA"
                                           required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        A descriptive name for this SLA policy
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority_id">
                                        Ticket Priority <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('priority_id') is-invalid @enderror" 
                                            id="priority_id" 
                                            name="priority_id" 
                                            required>
                                        <option value="">Select Priority</option>
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority->id }}" 
                                                    {{ old('priority_id') == $priority->id ? 'selected' : '' }}>
                                                {{ $priority->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('priority_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        This policy will apply to tickets with this priority
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="3" 
                                              placeholder="Describe when and how this SLA policy should be used">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SLA Timeframes -->
                        <h5 class="mt-4 mb-3">
                            <i class="fas fa-clock"></i> SLA Timeframes
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="response_time">
                                        First Response Time (minutes) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('response_time') is-invalid @enderror" 
                                           id="response_time" 
                                           name="response_time" 
                                           value="{{ old('response_time') }}" 
                                           min="1"
                                           placeholder="e.g., 60"
                                           required>
                                    @error('response_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Time allowed for first response (e.g., 60 = 1 hour, 1440 = 1 day)
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="resolution_time">
                                        Resolution Time (minutes) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('resolution_time') is-invalid @enderror" 
                                           id="resolution_time" 
                                           name="resolution_time" 
                                           value="{{ old('resolution_time') }}" 
                                           min="1"
                                           placeholder="e.g., 240"
                                           required>
                                    @error('resolution_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Time allowed to fully resolve the ticket (e.g., 240 = 4 hours, 4320 = 3 days)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="business_hours_only" 
                                               name="business_hours_only" 
                                               value="1"
                                               {{ old('business_hours_only', 1) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="business_hours_only">
                                            Calculate SLA during business hours only
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        <i class="fas fa-info-circle"></i> 
                                        Business hours: Monday to Friday, 8:00 AM - 5:00 PM. 
                                        When checked, SLA calculations will exclude weekends and after-hours.
                                        Uncheck for 24/7 SLA calculation.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Escalation Settings -->
                        <h5 class="mt-4 mb-3">
                            <i class="fas fa-exclamation-triangle"></i> Escalation Settings
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="escalation_time">
                                        Escalation Time (minutes)
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('escalation_time') is-invalid @enderror" 
                                           id="escalation_time" 
                                           name="escalation_time" 
                                           value="{{ old('escalation_time') }}" 
                                           min="1"
                                           placeholder="e.g., 120">
                                    @error('escalation_time')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Time before ticket should be escalated. Leave empty to disable auto-escalation.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="escalate_to_user_id">
                                        Escalate To User
                                    </label>
                                    <select class="form-control @error('escalate_to_user_id') is-invalid @enderror" 
                                            id="escalate_to_user_id" 
                                            name="escalate_to_user_id">
                                        <option value="">Select User (Optional)</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                    {{ old('escalate_to_user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('escalate_to_user_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        User to receive escalated tickets. Leave empty if escalation is not needed.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', 1) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            <strong>Active Policy</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted ml-4">
                                        Only active policies will be applied to tickets
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Time Presets -->
                        <div class="alert alert-info mt-4">
                            <h6><i class="fas fa-lightbulb"></i> Quick Time Reference:</h6>
                            <ul class="mb-0">
                                <li><strong>1 hour</strong> = 60 minutes</li>
                                <li><strong>4 hours</strong> = 240 minutes</li>
                                <li><strong>1 day</strong> = 1440 minutes</li>
                                <li><strong>3 days</strong> = 4320 minutes</li>
                                <li><strong>1 week</strong> = 10080 minutes</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create SLA Policy
                        </button>
                        <a href="{{ route('sla.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
