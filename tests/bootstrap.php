<?php

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');

$autoloader = realpath(__DIR__ . '/../vendor/autoload.php');

if (!file_exists($autoloader)) {
    $message = 'Composer generated autoloader does not exist. '
      . " Please run 'composer install' before running tests.";
    die($message . PHP_EOL);
}

$loader = require realpath(__DIR__ . '/../vendor/autoload.php');
$loader->add('QueryAuth\\', __DIR__);

define('APPLICATION_PATH', realpath(__DIR__ . '/..'));
