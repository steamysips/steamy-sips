<?php

class Login
{
    use Controller;

    function index()
    {
        $css_file = ROOT . "/styles/views/Login.css";

        $this->view('Login', [], 'Login', "<link rel=\"stylesheet\" href=\"$css_file\"></link>");
    }
}
