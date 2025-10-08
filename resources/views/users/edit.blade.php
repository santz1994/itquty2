@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Edit User
            <small>Modify user details and permissions</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('users.index') }}">Users</a></li>
            <li class="active">Edit {{ $user->name }}</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user-edit"></i> User Information
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="box-body">
                            <div class="form-group">
                                <label for="name">Full Name <span class="text-red">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
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
                                       value="{{ old('email', $user->email) }}" 
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
                                        <option value="{{ $role->name }}" 
                                                {{ old('role', $currentRole ?? '') === $role->name ? 'selected' : '' }}>
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
                                <label>
                                    <input type="checkbox" id="change_password" name="change_password" value="1"> 
                                    Change Password
                                </label>
                            </div>

                            <div id="password_fields" style="display: none;">
                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password">
                                    @error('password')
                                    <span class="help-block text-red">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update User
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
                            <i class="fa fa-info-circle"></i> User Details
                        </h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-condensed">
                            <tr>
                                <td><strong>User ID:</strong></td>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $user->updated_at->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Current Role:</strong></td>
                                <td>
                                    <span class="label label-{{ $currentRole === 'super-admin' ? 'danger' : ($currentRole === 'admin' ? 'warning' : ($currentRole === 'management' ? 'info' : 'default')) }}">
                                        {{ ucfirst(str_replace('-', ' ', $currentRole)) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-exclamation-triangle"></i> Security Notes
                        </h3>
                    </div>
                    <div class="box-body">
                        <p><strong>Role Changes:</strong></p>
                        <ul>
                            <li>Role changes take effect immediately</li>
                            <li>User may need to log out and back in</li>
                            <li>Password changes will force re-authentication</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('change_password').addEventListener('change', function() {
    var passwordFields = document.getElementById('password_fields');
    var passwordInput = document.getElementById('password');
    var confirmInput = document.getElementById('password_confirmation');
    
    if (this.checked) {
        passwordFields.style.display = 'block';
        passwordInput.required = true;
        confirmInput.required = true;
    } else {
        passwordFields.style.display = 'none';
        passwordInput.required = false;
        confirmInput.required = false;
        passwordInput.value = '';
        confirmInput.value = '';
    }
});
</script>
@endsection