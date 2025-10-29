@extends('layouts.app')

@section('main-content')

{{-- Page Header --}}
@include('components.page-header', [
    'title' => 'Create New User',
    'subtitle' => 'Add a new user to the system',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home'), 'icon' => 'home'],
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Create']
    ],
    'actions' => '<a href="'.route('users.index').'" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Back to List
    </a>'
])

<div class="container-fluid">
    <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user-plus"></i> User Information
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('users.store') }}" id="user-create-form">
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
                                <label for="division_id">Divisi <span class="text-red">*</span></label>
                                <select name="division_id" id="division_id" class="form-control @error('division_id') is-invalid @enderror" required>
                                    <option value="">Pilih Divisi...</option>
                                    @php
                                        $divs = $divisions ?? (\App\Division::orderBy('name')->get() ?? collect([]));
                                    @endphp
                                    @foreach($divs as $div)
                                        <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                    @endforeach
                                </select>
                                @error('division_id')<span class="help-block text-red">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">No HP</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')<span class="help-block text-red">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group">
                                <label for="role_id">Role <span class="text-red">*</span></label>
                                <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                    <option value="">Select a role...</option>
                                    @if(isset($roles) && $roles->count() > 0)
                                        @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $role->name)) }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No roles available</option>
                                    @endif
                                </select>
                                @error('role_id')<span class="help-block text-red">{{ $message }}</span>@enderror
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
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-save"></i> Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fa fa-times"></i> Cancel
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
</div>

{{-- Loading Overlay --}}
@include('components.loading-overlay')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form loading state
    $('#user-create-form').on('submit', function() {
        showLoading('Creating user...');
    });
});
</script>
@endpush
