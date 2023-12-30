<?php

class App
{
    private string $controller = 'Home';
    private string $method = 'index';

    // manage routes and methods 
    // $req = $_SERVER['REQUEST_URI'];
    //$method = $_SERVER['REQUEST_METHOD'];
    private function splitURL(): array
    {
        $URL = $_GET['url'] ?? 'home';
        return explode("/", trim($URL, '/'));
    }

    public function loadController(): void
    {
        $URL = $this->splitURL();
        /**
         * filename is relative to public/index.php
         */
        $filename = '../src/controllers/' . ucfirst($URL[0]) . '.php';

        // echo $filename;
        if (file_exists($filename)) {
            require $filename;
            $this->controller = ucfirst($URL[0]);
        } else {
            require '../src/controllers/_404.php';
            $this->controller = "_404";
        }

        $controller = new $this->controller;
        $controller->index(); # use get contents
    }
}

# include app html template here 
