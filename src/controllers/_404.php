<?php

class _404
{
    use Controller;

    function index()
    {
        $this->view('404.php');
    }
}
