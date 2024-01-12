<?php
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // setup configurations if website is running on localhost 

    define('DBHOST', 'localhost'); // do not change

    // define database credentials for localhost
    define('DBNAME', 'test_db'); // name of database
    define('DBUSER', 'root'); // name of database user
    define('DBPASS', 'aaa'); // password of database user

    // define absolute url for public folder
    define('ROOT', 'http://localhost/steamy-sips/public'); // do not change
} else {
    // setup configurations if website is not running on localhost
    // ignore this section if you are using localhost
    // define database credentials for another server
    define('DBNAME', '');
    define('DBHOST', '');
    define('DBUSER', '');
    define('DBPASS', '');

    // define absolute url for public folder
    define('ROOT', 'https://mywebsite.com');
}
