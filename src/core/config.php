<?php

declare(strict_types=1);

// Check for a custom header to switch to the testing environment
if ((isset($_SERVER['HTTP_X_TEST_ENV']) && $_SERVER['HTTP_X_TEST_ENV'] === 'testing')) {
    // a request is coming from the testing environment
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..', '.env.testing');
} elseif (defined('PHPUNIT_STEAMY_TESTSUITE') && PHPUNIT_STEAMY_TESTSUITE) {
    // application is currently being tested with phpunit => use testing database
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..', '.env.testing');
} else {
    // application is running normally => use production database
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
}

$dotenv->load();
