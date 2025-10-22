<?php
// Simple DB test script to check pg_connect using DATABASE_URL from .env if present
$env = parse_ini_file(__DIR__ . '/.env');
$databaseUrl = getenv('DATABASE_URL') ?: ($env['DATABASE_URL'] ?? null);
if (!$databaseUrl) {
    echo "No DATABASE_URL found in environment or .env\n";
    exit(1);
}
// Parse URL like: postgres://user:pass@host:5432/dbname?sslmode=require
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

// Try pg_connect
$conn = @pg_connect($dsn);
if ($conn) {
    echo "OK\n";
    pg_close($conn);
    exit(0);
} else {
    $err = error_get_last();
    echo "FAIL\n";
    if ($err) echo $err["message"] . "\n";
    exit(2);
}
