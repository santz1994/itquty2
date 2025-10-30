
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['title' => '', 'value' => '0', 'icon' => 'fa-exclamation', 'bgColor' => 'bg-red', 'link' => null]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['title' => '', 'value' => '0', 'icon' => 'fa-exclamation', 'bgColor' => 'bg-red', 'link' => null]); ?>
<?php foreach (array_filter((['title' => '', 'value' => '0', 'icon' => 'fa-exclamation', 'bgColor' => 'bg-red', 'link' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="info-box">
    <span class="info-box-icon <?php echo e($bgColor); ?>"><i class="fa <?php echo e($icon); ?>"></i></span>
    <div class="info-box-content">
        <span class="info-box-text"><?php echo e($title); ?></span>
        <span class="info-box-number"><?php echo e($value); ?></span>
        <?php if($link): ?>
            <span class="progress-description">
                <a href="<?php echo e($link); ?>" class="small">View Details →</a>
            </span>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views\imports\components\stats-card.blade.php ENDPATH**/ ?>