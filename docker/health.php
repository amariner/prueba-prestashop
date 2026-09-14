<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');
try {
    $data = getenv('BRISA_DATA_DIR') ?: '/data';
    if (!is_file($data . '/ready') || !is_file($data . '/seed-v1-complete')) { throw new RuntimeException(); }
    $config = require '/var/www/html/app/config/parameters.php';
    $p = $config['parameters'];
    $db = new PDO('mysql:host=' . $p['database_host'] . ';port=' . ($p['database_port'] ?: 3306) . ';dbname=' . $p['database_name'], $p['database_user'], $p['database_password'], [PDO::ATTR_TIMEOUT => 2]);
    $db->query('SELECT 1');
    echo '{"status":"ok","store":"brisa","mode":"demo"}';
} catch (Throwable $e) { http_response_code(503); echo '{"status":"starting"}'; }
