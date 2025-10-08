@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            User Management
            <small>Manage system users and their roles</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Users</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('error') }}
                </div>
                @endif

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-users"></i> System Users
                        </h3>
                        <div class="box-tools pull-right">
                            @can('create-users')
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add New User
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Roles</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                    <tr>
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
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-xs">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @can('edit-users')
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-xs">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete-users')
                                                @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs" 
                                                        onclick="return confirm('Are you sure you want to delete this user?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No users found.</td>
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
@endsection