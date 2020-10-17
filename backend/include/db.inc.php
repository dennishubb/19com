<?php

require_once __DIR__.'/../vendor/autoload.php';

$dbc = new MysqliDb([
    'host' => DB_HOST,
    'username' => DB_USER,
    'password' => DB_PASSWORD,
    'db' => DB_NAME,
    'port' => 3306,
    'charset' => 'utf8'
]);