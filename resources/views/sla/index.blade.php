@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-clock"></i> SLA Policies Management
                        </h3>
                        <div>
                            <a href="{{ route('sla.dashboard') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-line"></i> SLA Dashboard
                            </a>
                            @can('create', App\SlaPolicy::class)
                                <a href="{{ route('sla.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Create SLA Policy
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="20%">Policy Name</th>
                                    <th width="15%">Priority</th>
                                    <th width="15%">Response Time</th>
                                    <th width="15%">Resolution Time</th>
                                    <th width="10%">Business Hours</th>
                                    <th width="10%">Status</th>
                                    <th width="10%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($policies as $policy)
                                    <tr>
                                        <td>{{ $policy->id }}</td>
                                        <td>
                                            <strong>{{ $policy->name }}</strong>
                                            @if($policy->description)
                                                <br><small class="text-muted">{{ Str::limit($policy->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($policy->priority)
                                                <span class="badge badge-{{ $policy->priority->color ?? 'secondary' }}">
                                                    {{ $policy->priority->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">No Priority</span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-reply text-info"></i> 
                                            {{ $policy->response_time }} minutes
                                            <br><small class="text-muted">({{ formatMinutesToHumanReadable($policy->response_time) }})</small>
                                        </td>
                                        <td>
                                            <i class="fas fa-check-circle text-success"></i> 
                                            {{ $policy->resolution_time }} minutes
                                            <br><small class="text-muted">({{ formatMinutesToHumanReadable($policy->resolution_time) }})</small>
                                        </td>
                                        <td>
                                            @if($policy->business_hours_only)
                                                <span class="badge badge-info">
                                                    <i class="fas fa-business-time"></i> Business Hours
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock"></i> 24/7
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-toggle-status {{ $policy->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                    onclick="toggleStatus({{ $policy->id }})"
                                                    data-id="{{ $policy->id }}">
                                                <i class="fas fa-{{ $policy->is_active ? 'check' : 'times' }}"></i>
                                                {{ $policy->is_active ? 'Active' : 'Inactive' }}
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                @can('view', $policy)
                                                    <a href="{{ route('sla.show', $policy->id) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('update', $policy)
                                                    <a href="{{ route('sla.edit', $policy->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('delete', $policy)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="deletePolicy({{ $policy->id }})"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No SLA policies found.</p>
                                            @can('create', App\SlaPolicy::class)
                                                <a href="{{ route('sla.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Create First Policy
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($policies->hasPages())
                        <div class="mt-3">
                            {{ $policies->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
function toggleStatus(policyId) {
    if (confirm('Are you sure you want to toggle the status of this SLA policy?')) {
        $.ajax({
            url: `/sla/${policyId}/toggle-active`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message || 'Failed to toggle status');
                }
            },
            error: function(xhr) {
                toastr.error('An error occurred while toggling the status');
            }
        });
    }
}

function deletePolicy(policyId) {
    if (confirm('Are you sure you want to delete this SLA policy? This action cannot be undone.')) {
        const form = document.getElementById('delete-form');
        form.action = `/sla/${policyId}`;
        form.submit();
    }
}
</script>
@endsection

@php
if (!function_exists('formatMinutesToHumanReadable')) {
    function formatMinutesToHumanReadable($minutes) {
        if ($minutes < 60) {
            return $minutes . ' min';
        } elseif ($minutes < 1440) {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            return $hours . 'h' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . 'm' : '');
        } else {
            $days = floor($minutes / 1440);
            $remainingHours = floor(($minutes % 1440) / 60);
            return $days . 'd' . ($remainingHours > 0 ? ' ' . $remainingHours . 'h' : '');
        }
    }
}
@endphp
