@extends('layouts.app')

@section('main-content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Create New User
            <small>Add a new user to the system</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li><a href="{{ route('users.index') }}">Users</a></li>
            <li class="active">Create</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user-plus"></i> User Information
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('users.store') }}" role="form">
                        @csrf
                        <div class="box-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="list-unstyled">
                                        @foreach ($errors->all() as $error)
                                            <li><i class="fa fa-exclamation-circle"></i> {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                        <label for="name">Full Name <span class="text-red">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               placeholder="Enter full name"
                                               required>
                                        @if($errors->has('name'))
                                            <span class="help-block">{{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                        <label for="email">Email Address <span class="text-red">*</span></label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="Enter email address"
                                               required>
                                        @if($errors->has('email'))
                                            <span class="help-block">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                        <label for="password">Password <span class="text-red">*</span></label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Enter password"
                                               required>
                                        @if($errors->has('password'))
                                            <span class="help-block">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                                        <label for="password_confirmation">Confirm Password <span class="text-red">*</span></label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               placeholder="Confirm password"
                                               required>
                                        @if($errors->has('password_confirmation'))
                                            <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="phone" 
                                               name="phone" 
                                               value="{{ old('phone') }}" 
                                               placeholder="Enter phone number">
                                        @if($errors->has('phone'))
                                            <span class="help-block">{{ $errors->first('phone') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('division_id') ? 'has-error' : '' }}">
                                        <label for="division_id">Division <span class="text-red">*</span></label>
                                        <select class="form-control" id="division_id" name="division_id" required>
                                            <option value="">Select Division</option>
                                            @if(isset($divisions))
                                                @foreach($divisions as $division)
                                                    @if($division && is_object($division) && isset($division->name) && isset($division->id))
                                                        <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                                            {{ $division->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @if($errors->has('division_id'))
                                            <span class="help-block">{{ $errors->first('division_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group {{ $errors->has('role_id') ? 'has-error' : '' }}">
                                        <label for="role_id">User Role <span class="text-red">*</span></label>
                                        <select class="form-control" id="role_id" name="role_id" required>
                                            <option value="">Select Role</option>
                                            @if(isset($roles))
                                                @foreach($roles as $role)
                                                    @if($role && is_object($role) && isset($role->id) && isset($role->name))
                                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                            {{ $role->display_name ?? ucfirst($role->name) }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @if($errors->has('role_id'))
                                            <span class="help-block">{{ $errors->first('role_id') }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="is_active">Status</label>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" 
                                                       id="is_active" 
                                                       name="is_active" 
                                                       value="1" 
                                                       {{ old('is_active', 1) ? 'checked' : '' }}>
                                                Active User
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="callout callout-info">
                                <h4><i class="fa fa-info-circle"></i> Note:</h4>
                                <p>The new user will receive an email notification with their login credentials. Make sure to provide a valid email address.</p>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Back to Users
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Password strength indicator
    $('#password').on('keyup', function() {
        var password = $(this).val();
        var strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        var strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
        var strengthColor = ['danger', 'warning', 'info', 'primary', 'success'];
        
        if (password.length > 0) {
            if (!$('#password-strength').length) {
                $('#password').parent().append('<div id="password-strength" class="help-block"></div>');
            }
            $('#password-strength').html('<small class="text-' + strengthColor[strength-1] + '">Password Strength: ' + strengthText[strength-1] + '</small>');
        } else {
            $('#password-strength').remove();
        }
    });
    
    // Confirm password validation
    $('#password_confirmation').on('keyup', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                $(this).parent().removeClass('has-error').addClass('has-success');
                if (!$('#password-match').length) {
                    $(this).parent().append('<div id="password-match" class="help-block"></div>');
                }
                $('#password-match').html('<small class="text-success"><i class="fa fa-check"></i> Passwords match</small>');
            } else {
                $(this).parent().removeClass('has-success').addClass('has-error');
                if (!$('#password-match').length) {
                    $(this).parent().append('<div id="password-match" class="help-block"></div>');
                }
                $('#password-match').html('<small class="text-danger"><i class="fa fa-times"></i> Passwords do not match</small>');
            }
        } else {
            $(this).parent().removeClass('has-error has-success');
            $('#password-match').remove();
        }
    });
});
</script>
@endsection