<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

class Home
{
    use Controller;

    public function index(): void
    {
        $this->view(
            'Home',
            template_title: 'Home',
            template_tags: $this->getLibrariesTags(['aos', 'splide']),
            template_meta_description: "Welcome to Steamy Sips Café, where every sip is an experience. Step into our cozy world of aromatic delights, where the perfect brew meets community and conversation."
        );
    }
}
