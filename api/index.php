<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
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

    // Copy pre-migrated SQLite database or initialize
    $preMigrated = __DIR__ . '/../database/database.sqlite';
    if (!file_exists($sqliteDb) || filesize($sqliteDb) === 0) {
        if (file_exists($preMigrated) && filesize($preMigrated) > 0) {
            @copy($preMigrated, $sqliteDb);
        } else {
            @touch($sqliteDb);
        }
    }

    // 3. Register Composer Autoloader
    require __DIR__ . '/../vendor/autoload.php';

    // 4. Bootstrap Laravel Application
    /** @var \Illuminate\Foundation\Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->useStoragePath($storagePath);

    // 5. Explicitly handle HTTP request via Kernel
    /** @var \Illuminate\Contracts\Http\Kernel $kernel */
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    $request = \Illuminate\Http\Request::capture();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>FUNDI Serverless Startup Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
