{{-- Loading Overlay Component --}}
{{-- Usage: @include('components.loading-overlay', ['id' => 'loading', 'message' => 'Loading...']) --}}

<div class="loading-overlay" id="{{ $id ?? 'loading-overlay' }}" style="display: none;">
    <div class="loading-spinner">
        <i class="fa fa-spinner fa-spin fa-3x"></i>
        <p>{{ $message ?? 'Loading...' }}</p>
    </div>
</div>

@push('scripts')
<script>
    // Global loading functions
    window.showLoading = function(message = 'Loading...', id = '{{ $id ?? 'loading-overlay' }}') {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.querySelector('p').textContent = message;
            overlay.style.display = 'flex';
        }
    };
    
    window.hideLoading = function(id = '{{ $id ?? 'loading-overlay' }}') {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.style.display = 'none';
        }
    };
</script>
@endpush
