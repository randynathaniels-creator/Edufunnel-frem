<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
$dirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}
@unlink('/tmp/bootstrap/cache/config.php');
@unlink('/tmp/bootstrap/cache/packages.php');
@unlink('/tmp/bootstrap/cache/services.php');

define('LARAVEL_START', microtime(true));
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// DEBUG
echo "<pre>";
echo "config path: " . $app->configPath() . "\n";
echo "view config exists: " . (file_exists($app->configPath('view.php')) ? 'YES' : 'NO') . "\n";
echo "bound view: " . ($app->bound('view') ? 'YES' : 'NO') . "\n";
var_dump($app->getLoadedProviders());
echo "</pre>";
die();