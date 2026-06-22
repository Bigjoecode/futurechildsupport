<?php

$appConfig = require __DIR__ . '/app_config.php';

$dbconnec = mysqli_connect(
    $appConfig['db_host'],
    $appConfig['db_user'],
    $appConfig['db_pass'],
    $appConfig['db_name'],
    $appConfig['db_port']
);

if (!$dbconnec) {
    die('<p>Failed to connect to MySQL: ' . mysqli_connect_error() . '</p>');
}

mysqli_set_charset($dbconnec, 'utf8mb4');

future_child_support_ensure_tables($dbconnec);

function future_child_support_ensure_tables(mysqli $connection)
{
    $queries = [
        "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            full_name VARCHAR(190) NOT NULL,
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(80) DEFAULT NULL,
            subject VARCHAR(255) DEFAULT NULL,
            message TEXT NOT NULL,
            status VARCHAR(32) NOT NULL DEFAULT 'new',
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY idx_contact_messages_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    foreach ($queries as $query) {
        mysqli_query($connection, $query);
    }
}
