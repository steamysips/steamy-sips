<?php

session_start();
require '../src/core/init.php';

$app =  new App();
$app->loadController();
