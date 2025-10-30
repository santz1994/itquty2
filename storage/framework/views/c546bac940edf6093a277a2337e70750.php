
<?php
    $searchRoute = $searchRoute ?? request()->url();
    $searchPlaceholder = $searchPlaceholder ?? 'Search...';
    $searchValue = $searchValue ?? request('search');
?>

<div class="search-bar-container mb-3">
    <form method="GET" action="<?php echo e($searchRoute); ?>" class="search-form">
        <?php $__currentLoopData = request()->except(['search', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        
        <div class="input-group">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="<?php echo e($searchPlaceholder); ?>" 
                   value="<?php echo e($searchValue); ?>">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">
                    <i class="fa fa-search"></i>
                </button>
                <?php if($searchValue): ?>
                    <a href="<?php echo e($searchRoute); ?>" class="btn btn-default" title="Clear search">
                        <i class="fa fa-times"></i>
                    </a>
                <?php endif; ?>
            </span>
        </div>
    </form>
</div>

<style>
.search-bar-container {
    max-width: 400px;
    margin-bottom: 15px;
}

.search-form .input-group {
    width: 100%;
}

.search-form .input-group-btn .btn {
    border-left: 0;
}

.search-form .input-group-btn .btn:first-child {
    border-left: 1px solid #d2d6de;
}
</style><?php /**PATH D:\Project\ITQuty\quty2\resources\views\partials\search-bar.blade.php ENDPATH**/ ?>