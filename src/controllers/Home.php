<?php

class Home
{
    use Controller;

    function index()
    {
        $css_file = ROOT . "/styles/views/Home.css";

        $this->view('Home', [], 'Home', "<link rel=\"stylesheet\" href=\"$css_file\"></link>");
    }
}
