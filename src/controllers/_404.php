<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

class _404
{
    use Controller;

    public function index(): void
    {
        $this->view(
            '404',
            template_title: "Page not found",
            template_meta_description: "Oops! It seems you've wandered off the beaten path. Let us guide you back to the aromatic world of Steamy Sips. Return to our delightful offerings or explore anew. Flavorful surprises await your next click."
        );
    }
}
