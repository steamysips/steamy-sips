<?php

spl_autoload_register(function ($classname) {
    $filename = '../src/models/' . ucfirst($classname) . ".php";

    require $filename;
});

require 'config.php'; // define configurations for database and path
require 'functions.php'; // define global helper functions
require 'Database.php'; // define Database trait
require 'Model.php'; // define Model trait
require 'Controller.php'; // define Controller trait
require 'App.php';
