<?php
/**
 * Health check endpoint for Docker orchestration and monitoring.
 *
 * Returns a JSON response with status, database connectivity,
 * and uptime information. Use for container health checks,
 * load balancer pings, and monitoring alerts.
 *
 * Usage: GET /health.php
 * Response: {"status":"ok","db":"connected","timestamp":"..."}
 */

$start_time = microtime(true);
$response = [
    'status' => 'ok',
    'timestamp' => date('c'),
    'uptime' => null,
];

// Check database connectivity
try {
    require_once __DIR__ . '/config/db_connect.php';
    if ($pdo !== null) {
        $pdo->query('SELECT 1');
        $response['db'] = 'connected';
    } else {
        $response['db'] = 'unavailable';
        $response['status'] = 'degraded';
    }
} catch (\Throwable $e) {
    $response['db'] = 'error';
    $response['status'] = 'degraded';
    $response['db_error'] = $e->getMessage();
}

// Check storage directories are writable
$writable = true;
foreach (['storage/logs', 'storage/cache', 'uploads'] as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path) && !is_writable($path)) {
        $writable = false;
        $response['writable'][$dir] = false;
    }
}
if (!$writable) {
    $response['status'] = $writable ? 'ok' : 'degraded';
}

$response['duration_ms'] = round((microtime(true) - $start_time) * 1000, 2);
$response['php_version'] = PHP_VERSION;

$http_code = $response['status'] === 'ok' ? 200 : 503;
http_response_code($http_code);
header('Content-Type: application/json');
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
