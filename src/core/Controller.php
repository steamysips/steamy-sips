<?php

class Controller
{
    public function view($name)
    {
        $filename = '../src/views/' . ucfirst($name) . '.php';
        // echo $filename;
        if (file_exists($filename)) {
            require $filename;
        } else {
            require '../src/views/404.php';
        }
    }
}
