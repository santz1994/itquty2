
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['type' => 'unknown', 'size' => 'md']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['type' => 'unknown', 'size' => 'md']); ?>
<?php foreach (array_filter((['type' => 'unknown', 'size' => 'md']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $colors = [
        'duplicate_key' => '#DD4B39',
        'duplicate_record' => '#F39C12',
        'foreign_key_not_found' => '#3498DB',
        'invalid_data' => '#9B59B6',
        'business_rule_violation' => '#E74C3C',
    ];
    
    $icons = [
        'duplicate_key' => 'fa-key',
        'duplicate_record' => 'fa-clone',
        'foreign_key_not_found' => 'fa-unlink',
        'invalid_data' => 'fa-times-circle',
        'business_rule_violation' => 'fa-gavel',
    ];

    $color = $colors[$type] ?? '#95A5A6';
    $icon = $icons[$type] ?? 'fa-exclamation';
    $label = ucfirst(str_replace('_', ' ', $type));
?>

<span class="badge" style="background-color: <?php echo e($color); ?>">
    <i class="fa <?php echo e($icon); ?>"></i> <?php echo e($label); ?>

</span>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views\imports\components\conflict-badge.blade.php ENDPATH**/ ?>