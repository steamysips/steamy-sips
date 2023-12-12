<?php
$req = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$content = '';
$title = '';

//manage routes
if ($method === 'GET')
    switch ($req) {
        case '/':
            $content = file_get_contents('../src/views/Home.php');
            $title = 'Home';
            break;
        case '/register':
            $content = file_get_contents('../src/views/Register.php');
            $title = 'Registration';
            break;
        default:
            $content = file_get_contents('error.php');
            $title = '404';
            break;
    }

// manage API
if ($method === 'POST')
    switch ($req) {
        case '/register':
            include '../src/controllers/RegisterController.php';
            break;
        default:
            echo 'Bad request';
            break;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="LAMP template project" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/global.css">
    <!-- <script src="script.js" defer></script> -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🐞</text></svg>" />
    <title><?= $title ?> | LAMP</title>
</head>

<body>
    <div id="app">
        <!-- <h1><?= $title ?></h1> -->
        <?= $content ?>
    </div>
</body>

</html>