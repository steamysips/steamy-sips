<?php

class App
{
    private $controller = 'Home';
    private $method = 'index';

    private function show($stuff)
    {
        echo "<pre>";
        print_r($stuff);
        echo "</pre>";
    }

    private function splitURL()
    {

        $URL = $_GET['url'] ?? 'home';
        $URL = explode("/", $URL);
        return $URL;
    }

    public function loadController()
    {
        $URL = $this->splitURL();
        $filename = '../src/controllers/' . ucfirst($URL[0]) . '.php';
        // echo $filename;
        if (file_exists($filename)) {
            require $filename;
            $this->controller = ucfirst($URL[0]);
        } else {
            require '../src/controllers/_404.php';
            $this->controller = "_404";
        }

        $controller =  new $this->controller;
        $controller->index();
    }
}
