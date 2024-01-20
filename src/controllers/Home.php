<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Home
{
    use Controller;

    public function index(): void
    {
        $this->view('Home', [], 'Home');
    }
}
