{{-- Toastr Notifications Partial --}}
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.success('{{ session('success') }}');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.error('{{ session('error') }}');
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.warning('{{ session('warning') }}');
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.info('{{ session('info') }}');
        });
    </script>
@endif

{{-- For legacy session messages --}}
@if(session('status') && session('message'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('status') == 'success')
                toastr.success('{{ session('message') }}' + ({{ session('title') ? "' - ' + '" . session('title') . "'" : "''" }}));
            @elseif(session('status') == 'error')
                toastr.error('{{ session('message') }}' + ({{ session('title') ? "' - ' + '" . session('title') . "'" : "''" }}));
            @elseif(session('status') == 'warning')
                toastr.warning('{{ session('message') }}' + ({{ session('title') ? "' - ' + '" . session('title') . "'" : "''" }}));
            @else
                toastr.info('{{ session('message') }}' + ({{ session('title') ? "' - ' + '" . session('title') . "'" : "''" }}));
            @endif
        });
    </script>
@endif

{{-- Validation errors --}}
@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
        });
    </script>
@endif

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Toastr configuration
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Helper functions for manual toastr calls
window.showSuccess = function(message) {
    toastr.success(message);
};

window.showError = function(message) {
    toastr.error(message);
};

window.showWarning = function(message) {
    toastr.warning(message);
};

window.showInfo = function(message) {
    toastr.info(message);
};
</script>
@endpush