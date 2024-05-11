<?php

declare(strict_types=1);

/* Define character encoding
  Reference: https://phptherightway.com/#utf-8-at-the-php-level
*/
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

session_start(); // start a new session

require_once '../vendor/autoload.php'; // autoload classes. path is relative to public/index.php
require_once 'config.php'; // define configurations for database and root path