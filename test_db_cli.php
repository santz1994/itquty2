<?php
require __DIR__ . '/vendor/autoload.php';

// Load .env like Laravel
if (file_exists(__DIR__ . '/.env')) {
    try {
        $builder = Dotenv\Repository\RepositoryBuilder::createWithDefaultAdapters();
        $dot = Dotenv\Dotenv::createImmutable(__DIR__);
        $dot->load();
    } catch (Exception $e) {
        // ignore; getenv may still work
    }
}

$databaseUrl = getenv('DATABASE_URL');
if (!$databaseUrl) {
    echo "No DATABASE_URL found in environment or .env\n";
    exit(1);
}

$parts = parse_url($databaseUrl);
if ($parts === false) {
    echo "Failed to parse DATABASE_URL\n";
    exit(1);
}

$user = $parts['user'] ?? '';
$pass = $parts['pass'] ?? '';
$host = $parts['host'] ?? '';
$port = $parts['port'] ?? 5432;
$path = ltrim($parts['path'] ?? '', '/');
$query = [];
if (!empty($parts['query'])) parse_str($parts['query'], $query);
$sslmode = $query['sslmode'] ?? '';
$dsn = "host=$host port=$port dbname=$path user=$user password=$pass";
if ($sslmode) $dsn .= " sslmode=$sslmode";

echo "Attempting pg_connect with: $host:$port (sslmode={$sslmode})\n";

$conn = @pg_connect($dsn);
if ($conn) {
    echo "OK\n";
    pg_close($conn);
    exit(0);
} else {
    echo "FAIL\n";
    // Try to give a helpful error if pg_last_error exists
    if (function_exists('pg_last_error')) {
        $errno = pg_last_error();
        if ($errno) echo $errno . "\n";
    }
    exit(2);
}
