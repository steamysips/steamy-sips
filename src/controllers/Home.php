<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;
use Steamy\Core\Utility;

class Home
{
    use Controller;

    private function validateURL(): bool
    {
        return Utility::getURL() === 'home';
    }

    private function handleInvalidURL(): void
    {
        if (!$this->validateURL()) {
            (new _404())->index();
            die();
        }
    }

    public function index(): void
    {
        $this->handleInvalidURL();
        $this->view(
            'Home',
            template_title: 'Home',
            template_tags: $this->getLibrariesTags(['aos', 'splide']),
            template_meta_description: "Welcome to Steamy Sips Café, where every sip is an experience.
             Step into our cozy world of aromatic delights, where the perfect brew meets community and conversation."
        );
    }
}
