<!DOCTYPE html>
<html lang="en">

@section('htmlheader')
    @include('layouts.partials.htmlheader')
@show

<body class="skin-blue sidebar-mini">
<div class="wrapper">

    @include('layouts.partials.mainheader')

    @include('layouts.partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        @include('layouts.partials.contentheader')

        <!-- Main content -->
        <section class="content">
            <!-- Your Page Content Here -->
            @yield('main-content')

            {{-- Testing-only: expose flash and validation messages in page HTML so legacy BrowserKit-style tests can assert on them --}}
            @if (app()->environment('testing'))
                <div id="__test_helpers__" style="display:none">
                    <div id="__flash_status">{{ session('status') }}</div>
                    <div id="__flash_title">{{ session('title') }}</div>
                    <div id="__flash_message">{{ session('message') }}</div>
                    <div id="__flash_generic">{{ session('flash_message') ?? session('flash') }}</div>
                    <div id="__validation_errors">
                        @if (isset($errors) && $errors->any())
                            @foreach ($errors->all() as $err)
                                <div class="__err">{{ $err }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    @include('layouts.partials.controlsidebar')

    @yield('footer')
    @include('layouts.partials.footer')

</div><!-- ./wrapper -->

@section('scripts')
    @include('layouts.partials.scripts')
@show

</body>
</html>
