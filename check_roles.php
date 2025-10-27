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

    echo "=== USERS ===\n";
    $stmt = $pdo->query("SELECT id, name, email FROM users LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}\n";
    }

    echo "\n=== ROLES ===\n";
    $stmt = $pdo->query("SELECT id, name FROM roles");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Name: {$row['name']}\n";
    }

    echo "\n=== USER_ROLES ===\n";
    $stmt = $pdo->query("
        SELECT mhr.model_id, r.name 
        FROM model_has_roles mhr
        JOIN roles r ON mhr.role_id = r.id
        LIMIT 20
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "User ID: {$row['model_id']}, Role: {$row['name']}\n";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
