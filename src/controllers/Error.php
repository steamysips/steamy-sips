<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Error
{
    use Controller;

    public function index(string $error_message = ''): void
    {
        $this->view(
            'Error',
            template_title: "Error",
            template_meta_description: $error_message,
            enableIndexing: false
        );
    }
}
