<?php

// 1. Ensure all storage and view directories exist in /tmp (writable in Vercel serverless)
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app/public',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 2. Set serverless environment variables
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');

// 3. Setup SQLite database if DB_HOST is not provided
$dbConnection = getenv('DB_CONNECTION') ?: 'sqlite';
if ($dbConnection === 'sqlite' || !getenv('DB_HOST')) {
    $sqliteDb = '/tmp/database.sqlite';
    putenv('DB_CONNECTION=sqlite');
    putenv("DB_DATABASE={$sqliteDb}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $sqliteDb;

    if (!file_exists($sqliteDb) || filesize($sqliteDb) === 0) {
        touch($sqliteDb);
        $needsSeed = true;
    }
}

// 4. Forward request to Laravel public/index.php
require __DIR__ . '/../public/index.php';

// 5. Auto-migrate SQLite on fresh boot
if (isset($needsSeed) && $needsSeed && isset($app)) {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    } catch (\Throwable $e) {
        // Fallback gracefully
    }
}
