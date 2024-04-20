<?php

declare(strict_types=1);

require '../src/core/init.php'; // initialize app

$app = new Steamy\Core\App();
$app->loadController();
