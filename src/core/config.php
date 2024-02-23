<?php

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_ENV['APP_ENV'] == 'dev') {
    // setup configurations localhost server
    define('DB_HOST', 'localhost');

    // define absolute url for public folder
    define('ROOT', $_ENV['DEV_ROOT']);
}

if ($_ENV['APP_ENV'] == 'prod') {
    // setup configurations for production server
    define('DB_HOST', $_ENV['PROD_DB_HOST']);
    define('ROOT', $_ENV['PROD_ROOT']);
}

// define database credentials
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);