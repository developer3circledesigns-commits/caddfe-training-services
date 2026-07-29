<?php

$db_host = getenv('DB_HOST');
$db_port = getenv('DB_PORT');
$db_name = getenv('DB_DATABASE');
$db_user = getenv('DB_USERNAME');
$db_pass = getenv('DB_PASSWORD');

if (empty($db_host)) {
    $cfgFile = __DIR__ . '/app.php';
    if (file_exists($cfgFile)) {
        $cfg = require $cfgFile;
        $db_host = $cfg['db']['host'] ?? '127.0.0.1';
        $db_port = $cfg['db']['port'] ?? '3306';
        $db_name = $cfg['db']['database'] ?? 'u814177917_caddfe';
        $db_user = $cfg['db']['username'] ?? 'root';
        $db_pass = $cfg['db']['password'] ?? '';
    } else {
        $db_host = '127.0.0.1';
        $db_port = '3306';
        $db_name = 'u814177917_caddfe';
        $db_user = 'root';
        $db_pass = '';
    }
}

$dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    $pdo = null;
}

function db(): ?PDO
{
    global $pdo;
    return $pdo;
}
