<?php

if ($_SERVER['SERVER_NAME'] ?? null == 'localhost') {
    // setup configurations if code is running on localhost
    define('DB_HOST', 'localhost'); // ! do not change

    // define absolute url for public folder
    define('ROOT', 'http://localhost/steamy-sips/public'); // ! do not change

    // define production database credentials
    define('DB_NAME', 'test_db');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'aaa');
} else {
    // server is not localhost => testing ongoing
    define('DB_HOST', '');
    define('ROOT', '');

    // define test database credentials
    define('DB_NAME', 'test_db');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'aaa');
}
