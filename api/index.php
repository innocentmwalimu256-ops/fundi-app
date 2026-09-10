<?php

// Ensure all errors are logged and viewable
ini_set('display_errors', '1');
error_reporting(E_ALL);

// 1. Setup writable storage directories in /tmp for Vercel Serverless
$storagePath = '/tmp/storage';
$tmpDirs = [
    $storagePath . '/app',
    $storagePath . '/app/public',
    $storagePath . '/framework',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Set environment variables
putenv("LARAVEL_STORAGE_PATH={$storagePath}");
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";

putenv("APP_CONFIG_CACHE=/tmp/config.php");
putenv("APP_EVENTS_CACHE=/tmp/events.php");
putenv("APP_PACKAGES_CACHE=/tmp/packages.php");
putenv("APP_ROUTES_CACHE=/tmp/routes.php");
putenv("APP_SERVICES_CACHE=/tmp/services.php");

putenv("SESSION_DRIVER=cookie");
$_ENV['SESSION_DRIVER'] = 'cookie';

putenv("CACHE_STORE=array");
$_ENV['CACHE_STORE'] = 'array';

putenv("LOG_CHANNEL=stderr");
$_ENV['LOG_CHANNEL'] = 'stderr';

putenv("APP_KEY=base64:FIeQrllJrhhRUmbJwhiOE1vL/dL6sTY/dCu9BfvTiDE=");
$_ENV['APP_KEY'] = 'base64:FIeQrllJrhhRUmbJwhiOE1vL/dL6sTY/dCu9BfvTiDE=';

$sqliteDb = '/tmp/database.sqlite';
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$sqliteDb}");
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $sqliteDb;

$needsSeed = false;
if (!file_exists($sqliteDb) || filesize($sqliteDb) === 0) {
    @touch($sqliteDb);
    $needsSeed = true;
}

// 3. Register Composer Autoloader
require __DIR__ . '/../vendor/autoload.php';

// 4. Bootstrap Laravel Application
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Point storage path to writable /tmp
$app->useStoragePath($storagePath);

// 5. If fresh database, run migrations and seeders before processing request
if ($needsSeed) {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    } catch (\Throwable $e) {
        error_log("Database initialization notice: " . $e->getMessage());
    }
}

// 6. Capture request and send response
$request = \Illuminate\Http\Request::capture();
$response = $app->handleRequest($request);
$response->send();
$app->terminate();
