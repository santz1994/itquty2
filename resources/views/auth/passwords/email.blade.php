@extends('layouts.auth')

@section('htmlheader_title')
    Password recovery
@endsection

@section('main-content')

<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/home') }}"><b>Quty</b>Assets</a>
        </div><!-- /.login-logo -->

        <div class="login-box-body">
            <h4 class="text-center" style="margin-bottom:18px;">Reset your password</h4>

            {{-- Status message (success) --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    {{ session('status') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Whoops!</strong> There were some problems with your input.
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-muted text-center">Enter the email associated with your account and we'll send a password reset link.</p>

            <form action="{{ url('/password/email') }}" method="post" novalidate>
                @csrf

                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email" class="control-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                        <input id="email" type="email" name="email" class="form-control" placeholder="you@example.com" value="{{ old('email') }}" required autofocus aria-describedby="emailHelp">
                    </div>
                    <small id="emailHelp" class="form-text text-muted">We'll email you a link to reset your password.</small>
                    @if ($errors->has('email'))
                        <span class="help-block text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Send Password Reset Link</button>
                    </div>
                </div>
            </form>

            <div class="text-center" style="margin-top:12px;">
                <a href="{{ url('/login') }}">Back to login</a>
            </div>

        </div><!-- /.login-box-body -->

    </div><!-- /.login-box -->

    @include('layouts.partials.scripts_auth')

    <script>
        // Initialize iCheck if present
        if (typeof $.fn.iCheck === 'function') {
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%'
                });
            });
        }

        // Minimal client-side enhancement: focus email on load
        document.addEventListener('DOMContentLoaded', function () {
            var email = document.getElementById('email');
            if (email) email.focus();
        });
    </script>
</body>
<footer>
    <div class="container">
        <p class="text-center" style="margin: 2px; padding: 20px 0; color: #fff9f9;">
            &copy; {{ date('Y') }} Quty Assets. All rights reserved.
        </p>
    </div>
</footer>

@endsection

