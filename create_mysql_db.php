<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $reset = isset($argv[1]) && $argv[1] === 'reset';
    if ($reset) {
        $pdo->exec('DROP DATABASE IF EXISTS fundi_db;');
    }

    $pdo->exec('CREATE DATABASE IF NOT EXISTS fundi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');
    echo "MYSQL_CONNECTED_SUCCESS: Database 'fundi_db' is ready.\n";
} catch (PDOException $e) {
    echo "MYSQL_ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
