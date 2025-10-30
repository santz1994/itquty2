<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$db = $app['db'];

echo "=== TICKET_HISTORY INDEXES ===\n";
$indexes = $db->select("SHOW INDEXES FROM ticket_history WHERE Key_name != 'PRIMARY'");
foreach ($indexes as $idx) {
    echo "{$idx->Key_name}: {$idx->Column_name}\n";
}

echo "\n=== DAILY_ACTIVITIES INDEXES ===\n";
$indexes = $db->select("SHOW INDEXES FROM daily_activities WHERE Key_name != 'PRIMARY'");
foreach ($indexes as $idx) {
    echo "{$idx->Key_name}: {$idx->Column_name}\n";
}

echo "\n=== TICKET_COMMENTS INDEXES ===\n";
$indexes = $db->select("SHOW INDEXES FROM ticket_comments WHERE Key_name != 'PRIMARY'");
foreach ($indexes as $idx) {
    echo "{$idx->Key_name}: {$idx->Column_name}\n";
}

echo "\n=== TICKET_ASSETS INDEXES ===\n";
$indexes = $db->select("SHOW INDEXES FROM ticket_assets WHERE Key_name != 'PRIMARY'");
foreach ($indexes as $idx) {
    echo "{$idx->Key_name}: {$idx->Column_name}\n";
}
