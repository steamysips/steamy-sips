<?php
function show($stuff): void
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
}


function redirect($path): void
{
    header("Location: " . ROOT . "/" . $path);
    die;
}
