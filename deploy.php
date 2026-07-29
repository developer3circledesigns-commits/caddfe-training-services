<?php
/**
 * Post-deployment script — run after each deploy.
 * Restores config/app.php from the example file.
 *
 * Access: https://yoursite.com/deploy.php?key=your-secret-key
 * Or run via CLI: php deploy.php
 */

$secret = 'change-this-to-a-random-key';

if (PHP_SAPI !== 'cli') {
    if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }
}

$example = __DIR__ . '/config/app.php.example';
$target = __DIR__ . '/config/app.php';

if (!file_exists($target) && file_exists($example)) {
    copy($example, $target);
    echo 'config/app.php restored from example.' . PHP_EOL;
} elseif (file_exists($target)) {
    echo 'config/app.php already exists.' . PHP_EOL;
} else {
    echo 'config/app.php.example not found.' . PHP_EOL;
}
