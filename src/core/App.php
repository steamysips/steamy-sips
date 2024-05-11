<?php

declare(strict_types=1);

namespace Steamy\Core;

use Steamy\Controller\_404;
use Steamy\Controller\API;

class App
{
    /**
     * Calls appropriate controller class to deal with URL.
     * @return void
     */
    public function loadController(): void
    {
        $URL = Utility::splitURL();

        switch ($URL[0]) {
            case 'api':
                (new API())->index();
                break;
            default:
                $controllerClassName = 'Steamy\\Controller\\' . ucfirst($URL[0]);

                if (class_exists($controllerClassName)) {
                    // call appropriate controller
                    (new $controllerClassName())->index();
                } else {
                    // Fallback to 404 controller
                    (new _404())->index();
                }
        }
    }
}