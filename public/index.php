<?php
/* https://phptherightway.com/#utf-8-at-the-php-level */
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

session_start();
require '../src/core/init.php';

$app = new App();
$app->loadController();
