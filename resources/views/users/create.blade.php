@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Create New User
            <small>Add a new user to the system</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('users.index') }}">Users</a></li>
            <li class="active">Create</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user-plus"></i> User Information
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="box-body">
                            <div class="form-group">
                                <label for="name">Full Name <span class="text-red">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required>
                                @error('name')
                                <span class="help-block text-red">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address <span class="text-red">*</span></label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                <span class="help-block text-red">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="role">User Role <span class="text-red">*</span></label>
                                <select class="form-control @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">Select a role...</option>
                                    @if(isset($roles) && $roles->count() > 0)
                                        @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                        </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No roles available</option>
                                    @endif
                                </select>
                                @error('role')
                                <span class="help-block text-red">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Password <span class="text-red">*</span></label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                <span class="help-block text-red">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password <span class="text-red">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-info-circle"></i> Role Information
                        </h3>
                    </div>
                    <div class="box-body">
                        <h4>Role Descriptions:</h4>
                        <ul>
                            <li><strong>super-admin:</strong> Full system access including user management and system settings</li>
                            <li><strong>admin:</strong> Can manage assets, tickets, and users but cannot modify system settings</li>
                            <li><strong>management:</strong> Can view reports, KPI dashboard, and has oversight capabilities</li>
                            <li><strong>user:</strong> Basic access to view and create tickets, view assigned assets</li>
                        </ul>
                    </div>
                </div>

                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-exclamation-triangle"></i> Security Notes
                        </h3>
                    </div>
                    <div class="box-body">
                        <p><strong>Password Requirements:</strong></p>
                        <ul>
                            <li>Minimum 8 characters</li>
                            <li>Must be confirmed</li>
                            <li>User will be required to change password on first login</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection