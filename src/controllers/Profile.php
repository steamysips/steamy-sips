<?php

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Profile
{
    use Controller;

    public function index(): void
    {
        $this->view('Profile');
    }
}
