<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;

class _404
{
    use Controller;

    public function index(): void
    {
        $this->view('404.php');
    }
}
