<?php
if ($_SERVER['SERVER_NAME'] == 'localhost') {

    // define database credentials for localhost
    define('DBNAME', 'test_db');
    define('DBHOST', 'localhost');
    define('DBUSER', 'root');
    define('DBPASS', 'aaa');

    // define absolute url for public folder
    define('ROOT', 'http://localhost/steamy-sips/public');
} else {

    // define database credentials for another server
    define('DBNAME', '');
    define('DBHOST', '');
    define('DBUSER', '');
    define('DBPASS', '');

    // define absolute url for public folder
    define('ROOT', 'https://mywebsite.com');
}
