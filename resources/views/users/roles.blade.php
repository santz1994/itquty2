@extends('layouts.app')

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            User Roles Management
            <small>Manage user roles and permissions</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('users.index') }}">Users</a></li>
            <li class="active">Roles</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-users"></i> User Roles Overview
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-primary btn-sm" onclick="alert('Role management feature to be implemented')">
                                <i class="fa fa-cog"></i> Manage Roles
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        @if(isset($roles) && count($roles) > 0)
                            <div class="row">
                                @foreach($roles as $role)
                                <div class="col-md-6 col-lg-4">
                                    <div class="box box-widget">
                                        <div class="box-header with-border">
                                            <div class="user-block">
                                                <span class="username">
                                                    <a href="#">{{ ucfirst($role->name) }}</a>
                                                    <span class="label label-{{ $role->name == 'super-admin' ? 'danger' : ($role->name == 'admin' ? 'warning' : 'info') }} pull-right">
                                                        {{ $role->users->count() }} {{ $role->users->count() == 1 ? 'User' : 'Users' }}
                                                    </span>
                                                </span>
                                                <span class="description">Role: {{ $role->name }}</span>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            @if($role->users->count() > 0)
                                                <h5>Users with this role:</h5>
                                                <ul class="list-unstyled">
                                                    @foreach($role->users->take(5) as $user)
                                                    <li>
                                                        <i class="fa fa-user text-muted"></i> 
                                                        {{ $user->name }}
                                                        <small class="text-muted">({{ $user->email }})</small>
                                                    </li>
                                                    @endforeach
                                                    @if($role->users->count() > 5)
                                                        <li class="text-muted">
                                                            <i class="fa fa-plus"></i> 
                                                            {{ $role->users->count() - 5 }} more users...
                                                        </li>
                                                    @endif
                                                </ul>
                                            @else
                                                <p class="text-muted">No users assigned to this role.</p>
                                            @endif
                                        </div>
                                        <div class="box-footer">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info" onclick="viewRoleDetails('{{ $role->name }}')">
                                                    <i class="fa fa-eye"></i> View Details
                                                </button>
                                                @if(auth()->user()->hasRole('super-admin'))
                                                <button type="button" class="btn btn-warning" onclick="editRole('{{ $role->id }}')">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="callout callout-info">
                                <h4><i class="fa fa-info-circle"></i> No Roles Found</h4>
                                <p>No user roles are currently defined in the system.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Statistics -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-bar-chart"></i> Role Distribution
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($roles) && count($roles) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Role Name</th>
                                            <th>Display Name</th>
                                            <th>Users Count</th>
                                            <th>Permissions</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($roles as $role)
                                        <tr>
                                            <td>
                                                <span class="label label-{{ $role->name == 'super-admin' ? 'danger' : ($role->name == 'admin' ? 'warning' : 'info') }}">
                                                    {{ $role->name }}
                                                </span>
                                            </td>
                                            <td>{{ ucwords(str_replace('-', ' ', $role->name)) }}</td>
                                            <td>
                                                <span class="badge bg-blue">{{ $role->users->count() }}</span>
                                            </td>
                                            <td>
                                                @if($role->permissions->count() > 0)
                                                    <span class="badge bg-green">{{ $role->permissions->count() }} permissions</span>
                                                @else
                                                    <span class="text-muted">No permissions</span>
                                                @endif
                                            </td>
                                            <td>{{ $role->created_at ? $role->created_at->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-xs">
                                                    <button class="btn btn-info" onclick="viewRoleUsers({{ $role->id }})">
                                                        <i class="fa fa-users"></i>
                                                    </button>
                                                    @if(auth()->user()->hasRole('super-admin'))
                                                    <button class="btn btn-warning" onclick="alert('Role editing feature to be implemented')">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Role Details Modal -->
<div class="modal fade" id="roleDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Role Details</h4>
            </div>
            <div class="modal-body" id="roleDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewRoleDetails(roleName) {
    $('#roleDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
    $('#roleDetailsModal').modal('show');
    
    // Simulate loading role details
    setTimeout(function() {
        var content = '<h5>Role: ' + roleName.charAt(0).toUpperCase() + roleName.slice(1) + '</h5>';
        content += '<p>This would show detailed information about the role, including permissions and capabilities.</p>';
        content += '<p><em>Feature to be implemented</em></p>';
        
        $('#roleDetailsContent').html(content);
    }, 1000);
}

function viewRoleUsers(roleId) {
    alert('View users for role ID: ' + roleId + '\n(Feature to be implemented)');
}

function editRole(roleId) {
    alert('Edit role ID: ' + roleId + '\n(Feature to be implemented)');
}
</script>
@endsection