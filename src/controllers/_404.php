<?php

class _404
{
    use Controller;

    function index(): void
    {
        $this->view('404.php');
    }
}
