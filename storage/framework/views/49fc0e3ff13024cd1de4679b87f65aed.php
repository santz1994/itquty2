
<?php
    $status = $status ?? 'unknown';
    $statusText = $statusText ?? ucfirst($status);
    
    // Define status classes
    $statusClasses = [
        'active' => 'label-success',
        'inactive' => 'label-default',
        'pending' => 'label-warning',
        'approved' => 'label-success',
        'rejected' => 'label-danger',
        'completed' => 'label-success',
        'in-progress' => 'label-info',
        'open' => 'label-warning',
        'closed' => 'label-success',
        'assigned' => 'label-info',
        'unassigned' => 'label-default',
        'high' => 'label-danger',
        'medium' => 'label-warning',
        'low' => 'label-success',
        'critical' => 'label-danger',
        'maintenance' => 'label-warning',
        'retired' => 'label-default',
        'available' => 'label-success',
        'unavailable' => 'label-danger',
        'overdue' => 'label-danger',
        'unknown' => 'label-default'
    ];
    
    $badgeClass = $statusClasses[strtolower($status)] ?? 'label-default';
?>

<span class="label <?php echo e($badgeClass); ?>"><?php echo e($statusText); ?></span><?php /**PATH D:\Project\ITQuty\quty2\resources\views\partials\status-badge.blade.php ENDPATH**/ ?>