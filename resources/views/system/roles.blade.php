@extends('layouts.app')

@section('main-content')
    <section class="content-header">
        <h1>
            Roles Management
            <small>Manage user roles and their permissions</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <!-- Roles Overview -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-users"></i> System Roles
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-primary btn-sm" onclick="clearCache()">
                                <i class="fa fa-refresh"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
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
                                            {{ $role->users_count }} users
                                            <small>/ {{ $role->permissions_count }} permissions</small>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Details -->
        <div class="row">
            <div class="col-md-6">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-list"></i> Role Permissions
                        </h3>
                    </div>
                    <div class="box-body">
                        @foreach($roles as $role)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>{{ ucfirst(str_replace('-', ' ', $role->name)) }}</strong>
                                <span class="badge pull-right">{{ $role->permissions->count() }} permissions</span>
                            </div>
                            <div class="panel-body">
                                @if($role->permissions->count() > 0)
                                    <div class="row">
                                        @foreach($role->permissions->take(6) as $permission)
                                        <div class="col-md-6">
                                            <span class="label label-primary">{{ $permission->name }}</span>
                                        </div>
                                        @endforeach
                                        @if($role->permissions->count() > 6)
                                        <div class="col-md-12">
                                            <small class="text-muted">
                                                ... and {{ $role->permissions->count() - 6 }} more permissions
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                @else
                                    <em class="text-muted">No permissions assigned</em>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user"></i> User Assignments
                        </h3>
                    </div>
                    <div class="box-body">
                        @foreach($users as $user)
                        <div class="media">
                            <div class="media-left">
                                <span class="media-object">
                                    <i class="fa fa-user-circle fa-2x text-primary"></i>
                                </span>
                            </div>
                            <div class="media-body">
                                <h5 class="media-heading">{{ $user->name }}</h5>
                                <p class="text-muted">{{ $user->email }}</p>
                                <div>
                                    @foreach($user->roles as $role)
                                    <span class="label label-{{ $role->name === 'super-admin' ? 'danger' : ($role->name === 'admin' ? 'warning' : ($role->name === 'management' ? 'info' : 'success')) }}">
                                        {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-exclamation-triangle"></i> Role Management Actions
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Role Hierarchy:</strong>
                            <ul>
                                <li><strong>super-admin:</strong> Full system access including settings and user management</li>
                                <li><strong>admin:</strong> Management functions for assets, tickets, and users</li>
                                <li><strong>management:</strong> Reporting and oversight capabilities</li>
                                <li><strong>user:</strong> Basic functionality for assets and tickets</li>
                            </ul>
                        </div>
                        
                        <div class="btn-group" role="group">
                            <a href="{{ route('system.permissions') }}" class="btn btn-primary">
                                <i class="fa fa-key"></i> Manage Permissions
                            </a>
                            <a href="{{ route('users.index') }}" class="btn btn-success">
                                <i class="fa fa-users"></i> Manage Users
                            </a>
                            <button type="button" class="btn btn-warning" onclick="clearCache()">
                                <i class="fa fa-refresh"></i> Clear Cache
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function clearCache() {
    if (confirm('Are you sure you want to clear all caches? This may temporarily slow down the application.')) {
        fetch('{{ route("system.cache.clear") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ Error: ' + error.message);
        });
    }
}
</script>
@endsection
