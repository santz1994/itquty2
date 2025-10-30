
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['resolution' => 'skip']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['resolution' => 'skip']); ?>
<?php foreach (array_filter((['resolution' => 'skip']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php
    $labels = [
        'skip' => ['text' => 'Skip Row', 'class' => 'label-warning', 'icon' => 'fa-forward'],
        'create_new' => ['text' => 'Create New', 'class' => 'label-success', 'icon' => 'fa-plus'],
        'update_existing' => ['text' => 'Update Existing', 'class' => 'label-info', 'icon' => 'fa-pencil'],
        'merge' => ['text' => 'Merge Records', 'class' => 'label-primary', 'icon' => 'fa-compress'],
    ];

    $info = $labels[$resolution] ?? $labels['skip'];
?>

<span class="label <?php echo e($info['class']); ?>">
    <i class="fa <?php echo e($info['icon']); ?>"></i> <?php echo e($info['text']); ?>

</span>
<?php /**PATH D:\Project\ITQuty\quty2\resources\views\imports\components\resolution-badge.blade.php ENDPATH**/ ?>