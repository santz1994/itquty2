@extends('layouts.app')

@section('main-content')

@include('components.page-header', [
    'title' => 'User Management',
    'subtitle' => 'Manage system users and their roles',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('admin.dashboard'), 'icon' => 'home'],
        ['label' => 'Users']
    ],
    'actions' => auth()->user()->can('create-users') ? 
        '<a href="'.route('users.create').'" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New User
        </a>' : ''
])

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible auto-dismiss">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible auto-dismiss">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif

                <div class="box box-primary">
                    <div class="box-body">
                        <div class="table-responsive">
                            @can('delete-users')
                            <div class="table-toolbar" style="margin-bottom:10px;">
                                <button id="bulk-delete-btn" class="btn btn-danger" disabled>
                                    <i class="fa fa-trash"></i> Delete Selected
                                </button>
                            </div>
                            @endcan
                            <table class="table table-enhanced table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:40px; text-align:center;"> 
                                            <input type="checkbox" id="select-all" title="Select all">
                                        </th>
                                        <th class="sortable" data-column="id">ID</th>
                                        <th class="sortable" data-column="name">Name</th>
                                        <th class="sortable" data-column="email">Email</th>
                                        <th>Roles</th>
                                        <th class="sortable" data-column="created_at">Created</th>
                                        <th class="actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                    <tr>
                                        <td style="text-align:center; vertical-align: middle;">
                                            <input type="checkbox" class="row-check" data-id="{{ $user->id }}">
                                        </td>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                            @if($user->id === auth()->id())
                                            <span class="label label-info">You</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                            <span class="label label-{{ $role->name === 'super-admin' ? 'danger' : ($role->name === 'admin' ? 'warning' : ($role->name === 'management' ? 'info' : 'success')) }}">
                                                {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                            </span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="table-actions" style="white-space: nowrap;">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.show', $user) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="View Details">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" 
                                                   class="btn btn-warning btn-sm"
                                                   data-toggle="tooltip" 
                                                   title="Edit User">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm delete-confirm" 
                                                            data-item-name="user {{ $user->name }}"
                                                            data-toggle="tooltip" 
                                                            title="Delete User">
                                                        <i class="fa fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                                @else
                                                <button type="button" 
                                                        class="btn btn-secondary btn-sm" 
                                                        disabled
                                                        data-toggle="tooltip" 
                                                        title="Cannot delete yourself">
                                                    <i class="fa fa-ban"></i> Cannot Delete
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <div class="empty-state-icon">
                                                    <i class="fa fa-users"></i>
                                                </div>
                                                <div class="empty-state-title">No Users Found</div>
                                                <div class="empty-state-description">
                                                    There are no users matching your search criteria.
                                                    @can('create-users')
                                                    <br>Try adjusting your filters or create a new user.
                                                    @endcan
                                                </div>
                                                @can('create-users')
                                                <a href="{{ route('users.create') }}" class="btn btn-primary">
                                                    <i class="fa fa-plus"></i> Add New User
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($users->hasPages())
                        <div class="text-center">
                            {{ $users->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Role Summary -->
                <div class="row">
                    @foreach($roles as $role)
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-{{ $role->name === 'super-admin' ? 'red' : ($role->name === 'admin' ? 'yellow' : ($role->name === 'management' ? 'blue' : 'green')) }}">
                                <i class="fa fa-{{ $role->name === 'super-admin' ? 'crown' : ($role->name === 'admin' ? 'user-tie' : ($role->name === 'management' ? 'briefcase' : 'user')) }}"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ ucfirst(str_replace('-', ' ', $role->name)) }}</span>
                                <span class="info-box-number">
                                    {{ $users->filter(function($user) use ($role) {
                                        return $user->roles->contains('name', $role->name);
                                    })->count() }} users
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>

@include('components.loading-overlay')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Delete confirmation
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');
        var itemName = $(this).data('item-name') || 'this user';
        
        if (confirm('Are you sure you want to delete ' + itemName + '? This action cannot be undone.')) {
            showLoading('Deleting user...');
            form.submit();
        }
    });
    
    // Loading state for action buttons
    $('.btn-group a').on('click', function() {
        var action = $(this).attr('title') || 'Processing';
        showLoading(action + '...');
    });

    // Select all / row selection handling
    $('#select-all').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    $('.row-check').on('change', function() {
        var anyChecked = $('.row-check:checked').length > 0;
        $('#bulk-delete-btn').prop('disabled', !anyChecked);
    });

    // Bulk delete handler
    $('#bulk-delete-btn').on('click', function(e) {
        e.preventDefault();
        var ids = $('.row-check:checked').map(function() { return $(this).data('id'); }).get();
        if (!ids.length) return;

        if (!confirm('Are you sure you want to delete the selected users? This action cannot be undone.')) return;

        showLoading('Deleting selected users...');

        var token = '{{ csrf_token() }}';

        fetch('{{ route('users.bulk-delete') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ids: ids })
        }).then(function(resp) {
            if (resp.ok) return resp.json();
            throw new Error('Failed to delete');
        }).then(function(json) {
            hideLoading();
            if (json.success) {
                // Remove deleted rows from table
                ids.forEach(function(id) { $('.row-check[data-id="' + id + '"]').closest('tr').remove(); });
                $('#bulk-delete-btn').prop('disabled', true);
                showFlash('Users deleted successfully', 'success');
            } else {
                showFlash(json.message || 'Failed to delete users', 'error');
            }
        }).catch(function(err) {
            hideLoading();
            console.error(err);
            showFlash('An error occurred while deleting users', 'error');
        });
    });
});
</script>
@endpush
