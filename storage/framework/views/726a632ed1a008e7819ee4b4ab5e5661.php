


<div class="loading-overlay" id="<?php echo e($id ?? 'loading-overlay'); ?>" style="display: none;">
    <div class="loading-spinner">
        <i class="fa fa-spinner fa-spin fa-3x"></i>
        <p><?php echo e($message ?? 'Loading...'); ?></p>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    // Global loading functions
    window.showLoading = function(message = 'Loading...', id = '<?php echo e($id ?? 'loading-overlay'); ?>') {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.querySelector('p').textContent = message;
            overlay.style.display = 'flex';
        }
    };
    
    window.hideLoading = function(id = '<?php echo e($id ?? 'loading-overlay'); ?>') {
        const overlay = document.getElementById(id);
        if (overlay) {
            overlay.style.display = 'none';
        }
    };
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\Project\ITQuty\Quty1\resources\views/components/loading-overlay.blade.php ENDPATH**/ ?>