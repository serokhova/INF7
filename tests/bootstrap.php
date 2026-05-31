<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// (Re)create the test SQLite database once per suite run.
// SQLite creates the file on first connection, so we skip database:create
// (which is not supported by the SQLite platform).
$root = dirname(__DIR__);
$dbFile = $root . '/var/test.db';
if (file_exists($dbFile)) {
    unlink($dbFile);
}

passthru(sprintf(
    'php "%1$s/bin/console" --env=test doctrine:schema:create -q && '.
    'php "%1$s/bin/console" --env=test doctrine:fixtures:load -n -q',
    $root
));
