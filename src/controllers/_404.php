<?php

class _404
{
    use Controller;

    public function index(): void
    {
        $this->view('404.php');
    }
}
