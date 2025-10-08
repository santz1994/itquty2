<?php

echo "🔍 MYSQL CONNECTION DIAGNOSTICS\n";
echo "===============================\n\n";

// Test basic MySQL connection
$host = '127.0.0.1';
$port = '3306';
$database = 'itquty';
$username = 'root';
$password = '';

echo "Testing MySQL connection...\n";
echo "Host: {$host}:{$port}\n";
echo "Database: {$database}\n";
echo "Username: {$username}\n\n";

try {
    // Test basic connection without database
    $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password);
    echo "✅ MySQL server connection: SUCCESS\n";
    
    // Check if database exists
    $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array($database, $databases)) {
        echo "✅ Database '{$database}' exists: YES\n";
        
        // Test connection to specific database
        $pdo_db = new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password);
        echo "✅ Database connection: SUCCESS\n";
        
        // Check tables
        $tables = $pdo_db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        echo "📊 Tables found: " . count($tables) . "\n";
        
        if (count($tables) > 0) {
            echo "   Tables: " . implode(', ', array_slice($tables, 0, 5)) . (count($tables) > 5 ? '...' : '') . "\n";
        }
        
    } else {
        echo "❌ Database '{$database}' exists: NO\n";
        echo "📋 Available databases: " . implode(', ', $databases) . "\n";
        echo "\n🔧 SOLUTION: Create database '{$database}'\n";
        echo "   Run: CREATE DATABASE {$database};\n";
    }
    
} catch (PDOException $e) {
    echo "❌ MySQL connection failed: " . $e->getMessage() . "\n";
    echo "\n🔧 POSSIBLE SOLUTIONS:\n";
    echo "   1. Start MySQL service\n";
    echo "   2. Check MySQL credentials\n";
    echo "   3. Verify MySQL is running on port 3306\n";
}

echo "\n🔍 PHP EXTENSIONS CHECK\n";
echo "=======================\n";
$extensions = ['pdo', 'pdo_mysql', 'mysqli', 'mysqlnd'];
foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "{$status} {$ext}\n";
}

echo "\n💻 SYSTEM INFO\n";
echo "==============\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PDO Drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n";