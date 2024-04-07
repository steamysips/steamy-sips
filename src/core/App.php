<?php

declare(strict_types=1);

namespace Steamy\Core;

use Steamy\Controller\_404;

class App
{
    /**
     * Calls appropriate controller class to deal with URL.
     * @return void
     */
    public function loadController(): void
    {
        $URL = Utility::splitURL();

        $controllerClassName = 'Steamy\\Controller\\' . ucfirst($URL[0]);


        if (class_exists($controllerClassName)) {
            $controller = new $controllerClassName();
        } else {
            // Fallback to 404 controller
            $controller = new _404();
        }

        $controller->index(); // display contents
    }
}