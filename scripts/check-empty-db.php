<?php
$pdo = new PDO('mysql:host=' . getenv('DB_SERVER') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWD'));
if ($pdo->query('SHOW TABLES')->fetch()) {
    fwrite(STDERR, "Refusing installation: database already contains tables but /data/install-complete is missing. Restore matching volume/config or investigate the interrupted installation. No data was deleted.\n");
    exit(1);
}
