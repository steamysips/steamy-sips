<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Home
{
    use Controller;

    public function index(): void
    {
        $carousel_script = "<script src=\"https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js\"></script>";
        $this->view('Home', template_title: 'Home', template_tags: $carousel_script);
    }
}
