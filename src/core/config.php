<?php

declare(strict_types=1);

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

// define database credentials
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USERNAME', $_ENV['DB_USERNAME']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);


// Check for a custom header to switch to the testing environment
if (isset($_SERVER['HTTP_X_TEST_ENV']) && $_SERVER['HTTP_X_TEST_ENV'] === 'testing') {
    // a request is coming from the testing environment
    define('DB_NAME', $_ENV['TEST_DB_NAME']);
} elseif (defined('PHPUNIT_STEAMY_TESTSUITE') && PHPUNIT_STEAMY_TESTSUITE) {
    // application is currently being tested with phpunit => use testing database
    define('DB_NAME', $_ENV['TEST_DB_NAME']);
} else {
    // application is running normally => use production database
    define('DB_NAME', $_ENV['PROD_DB_NAME']);
}

