<?php
declare(strict_types=1);
for ($attempt = 0; $attempt < 60; ++$attempt) {
    try {
        new PDO('mysql:host=' . getenv('DB_SERVER') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWD'), [PDO::ATTR_TIMEOUT => 3]);
        echo "Database connected.\n";
        exit(0);
    } catch (PDOException $e) { sleep(3); }
}
fwrite(STDERR, "Database unavailable after 180 seconds; check Railway private networking and DB variables.\n");
exit(1);
