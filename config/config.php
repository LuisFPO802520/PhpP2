<?php
define('DB_PATH', __DIR__ . '/../data/database.sqlite');
define('DB_DSN', 'sqlite:' . DB_PATH);

define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);


//caso use postgres:
//define('DB_DSN', 'pgsql:host=localhost;dbname=bigbars;port=5432'); 
//define('DB_USER', 'teu_usuario');
//define('DB_PASS', 'tua_senha');