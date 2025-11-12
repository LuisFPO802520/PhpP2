<?php
define('DB_PATH', __DIR__ . '/../data/database.sqlite');
define('DB_DSN', 'sqlite:' . DB_PATH);

define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
