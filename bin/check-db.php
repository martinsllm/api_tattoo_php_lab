<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/bootstrap/env.php';

loadEnv(dirname(__DIR__) . '/.env');

use App\Database\Connection;

$connection = new Connection();
$pdo = $connection->pdo();

$count = (int) $pdo->query('SELECT COUNT(*) AS c FROM artists')->fetch()['c'];

echo "OK: connected to MySQL, artists count = {$count}\n";
