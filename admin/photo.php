<?php
require_once __DIR__ . '/../config/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit;
}

try {
    if ($pdo === null) throw new \RuntimeException('DB unavailable');
    $stmt = $pdo->prepare('SELECT photo_data, photo_mime FROM enrollments WHERE id = :id AND photo_data IS NOT NULL');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if ($row && !empty($row['photo_data'])) {
        header('Content-Type: ' . $row['photo_mime']);
        header('Cache-Control: public, max-age=3600');
        echo $row['photo_data'];
        exit;
    }
} catch (\Throwable $e) {
    error_log('Photo fetch error: ' . $e->getMessage());
}

http_response_code(404);