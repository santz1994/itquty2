<?php
// Load environment variables
$dotenv_file = __DIR__ . '/.env';
$env_vars = [];
if (file_exists($dotenv_file)) {
    $lines = file($dotenv_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $env_vars[trim($key)] = trim($value);
        }
    }
}

$host = $env_vars['DB_HOST'] ?? 'localhost';
$database = $env_vars['DB_DATABASE'] ?? 'itquty';
$user = $env_vars['DB_USERNAME'] ?? 'root';
$password = $env_vars['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== PERMISSIONS ===\n";
    $stmt = $pdo->query("SELECT id, name FROM permissions ORDER BY name");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Name: {$row['name']}\n";
    }

    echo "\n=== ROLE_HAS_PERMISSIONS ===\n";
    $stmt = $pdo->query("
        SELECT r.name AS role_name, p.name AS permission_name
        FROM role_has_permissions rhp
        JOIN roles r ON rhp.role_id = r.id
        JOIN permissions p ON rhp.permission_id = p.id
        WHERE r.name IN ('super-admin', 'admin')
        ORDER BY r.name, p.name
    ");
    $lastRole = '';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['role_name'] !== $lastRole) {
            echo "\n{$row['role_name']}:\n";
            $lastRole = $row['role_name'];
        }
        echo "  - {$row['permission_name']}\n";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
