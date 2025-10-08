@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Permissions Management
            <small>Manage system permissions and role assignments</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('system.settings') }}">System</a></li>
            <li class="active">Permissions</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!-- Permissions List -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-key"></i> System Permissions
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($permissions) && count($permissions) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Permission Name</th>
                                        <th>Guard</th>
                                        <th>Assigned Roles</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                    <tr>
                                        <td><strong>{{ $permission->name }}</strong></td>
                                        <td>{{ $permission->guard_name }}</td>
                                        <td>
                                            @if($permission->roles->count() > 0)
                                                @foreach($permission->roles as $role)
                                                <span class="label label-{{ $role->name === 'super-admin' ? 'danger' : ($role->name === 'admin' ? 'warning' : 'info') }}">
                                                    {{ $role->name }}
                                                </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No roles assigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $permission->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-xs">
                                                <button class="btn btn-info" onclick="editPermission({{ $permission->id }})">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                @if($permission->roles->count() === 0)
                                                <button class="btn btn-danger" onclick="deletePermission({{ $permission->id }})">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No permissions found.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permission Actions -->
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-plus"></i> Create Permission
                        </h3>
                    </div>
                    <div class="box-body">
                        <form method="POST" action="{{ route('system.permissions.create') }}">
                            @csrf
                            <div class="form-group">
                                <label for="permission_name">Permission Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="permission_name" 
                                       name="name" 
                                       placeholder="e.g., view-reports" 
                                       required>
                            </div>
                            <div class="form-group">
                                <label for="guard_name">Guard</label>
                                <select class="form-control" id="guard_name" name="guard_name">
                                    <option value="web">Web</option>
                                    <option value="api">API</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-plus"></i> Create Permission
                            </button>
                        </form>
                    </div>
                </div>

                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-link"></i> Assign to Role
                        </h3>
                    </div>
                    <div class="box-body">
                        <form method="POST" action="{{ route('system.permissions.assign') }}">
                            @csrf
                            <div class="form-group">
                                <label for="permission_id">Permission</label>
                                <select class="form-control" id="permission_id" name="permission_id" required>
                                    <option value="">Select Permission...</option>
                                    @if(isset($permissions))
                                        @foreach($permissions as $permission)
                                        <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="role_id">Role</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    <option value="">Select Role...</option>
                                    @if(isset($roles))
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fa fa-link"></i> Assign Permission
                            </button>
                        </form>
                    </div>
                </div>

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> Permission Info
                        </h3>
                    </div>
                    <div class="box-body">
                        <p><strong>Total Permissions:</strong> {{ isset($permissions) ? count($permissions) : 0 }}</p>
                        <p><strong>Total Roles:</strong> {{ isset($roles) ? count($roles) : 0 }}</p>
                        <p><strong>Unassigned Permissions:</strong> 
                            {{ isset($permissions) ? $permissions->filter(function($p) { return $p->roles->count() === 0; })->count() : 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-Permission Matrix -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-table"></i> Permission Matrix
                        </h3>
                    </div>
                    <div class="box-body">
                        @if(isset($roles) && isset($permissions) && count($roles) > 0 && count($permissions) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>Permission</th>
                                        @foreach($roles as $role)
                                        <th class="text-center">{{ ucfirst($role->name) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                    <tr>
                                        <td><strong>{{ $permission->name }}</strong></td>
                                        @foreach($roles as $role)
                                        <td class="text-center">
                                            @if($role->hasPermissionTo($permission->name))
                                                <i class="fa fa-check text-success"></i>
                                            @else
                                                <i class="fa fa-times text-danger"></i>
                                            @endif
                                        </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No data available for permission matrix.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function editPermission(permissionId) {
    // Implement permission editing
    alert('Edit permission ID: ' + permissionId + ' (Feature to be implemented)');
}

function deletePermission(permissionId) {
    if (confirm('Are you sure you want to delete this permission? This action cannot be undone.')) {
        // Implement permission deletion
        alert('Delete permission ID: ' + permissionId + ' (Feature to be implemented)');
    }
}
</script>
@endsection