<?php
// Manually parse .env to extract DATABASE_URL and test TCP and pg_connect connectivity
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo ".env not found\n";
    exit(1);
}
$contents = file_get_contents($envPath);
$matches = [];
if (!preg_match('/^\s*DATABASE_URL\s*=\s*(.+)\s*$/m', $contents, $matches)) {
    echo "DATABASE_URL not found in .env\n";
    exit(2);
}
$raw = trim($matches[1]);
// Remove surrounding quotes if present
if ((substr($raw,0,1) === '"' && substr($raw,-1) === '"') || (substr($raw,0,1)==="'" && substr($raw,-1)==="'")) {
    $raw = substr($raw,1,-1);
}

echo "Found DATABASE_URL (masked): ";
// mask password if present
$masked = preg_replace('/^(postgres:\/\/)([^:]+):(.*?)@(.*)$/', '$1$2:****@$4', $raw);
echo $masked . "\n";

$parts = parse_url($raw);
if ($parts === false) {
    echo "Failed to parse DATABASE_URL\n";
    exit(3);
}
$host = $parts['host'] ?? '';
$port = $parts['port'] ?? 5432;
echo "Host: $host\n";
echo "Port: $port\n";

echo "Checking TCP connection to $host:$port...\n";
$fp = @fsockopen($host, $port, $errno, $errstr, 5);
if ($fp) {
    echo "TCP CONNECT OK\n";
    fclose($fp);
} else {
    echo "TCP CONNECT FAIL: $errstr ($errno)\n";
}

if (function_exists('pg_connect')) {
    echo "pg_connect function available, attempting pg_connect...\n";
    $user = $parts['user'] ?? '';
    $pass = $parts['pass'] ?? '';
    $dbname = ltrim($parts['path'] ?? '', '/');
    parse_str($parts['query'] ?? '', $q);
    $sslmode = $q['sslmode'] ?? '';
    $dsn = "host=$host port=$port dbname=$dbname user=$user password=$pass";
    if ($sslmode) $dsn .= " sslmode=$sslmode";
    $conn = @pg_connect($dsn);
    if ($conn) {
        echo "pg_connect OK\n";
        pg_close($conn);
    } else {
        echo "pg_connect FAIL\n";
        if (function_exists('pg_last_error')) echo pg_last_error() . "\n";
    }
} else {
    echo "pg_connect not available in this PHP build\n";
}
