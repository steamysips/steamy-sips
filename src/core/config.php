<?php
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // setup configurations if website is running on localhost 

    define('DB_HOST', 'localhost'); // do not change

    // define database credentials for localhost
    define('DB_NAME', 'test_db'); // name of database
    define('DB_USERNAME', 'root'); // name of database user
    define('DB_PASSWORD', 'aaa'); // password of database user

    // define absolute url for public folder
    define('ROOT', 'http://localhost/steamy-sips/public'); // do not change
} else {
    // setup configurations if website is not running on localhost
    // ignore this section if you are using localhost
    // define database credentials for another server
    define('DB_NAME', '');
    define('DB_HOST', '');
    define('DB_USERNAME', '');
    define('DB_PASSWORD', '');

    // define absolute url for public folder
    define('ROOT', 'https://mywebsite.com');
}
