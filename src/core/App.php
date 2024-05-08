<?php

declare(strict_types=1);

namespace Steamy\Core;

use Steamy\Controller\Error;
use Steamy\Controller\API;
use Throwable;

class App
{

    /**
     * Global exception handler
     * @param Throwable $exception
     * @return void
     */
    public function exception_handler(Throwable $exception): void
    {
//        echo "Uncaught exception: ", $exception->getMessage(), "\n";
        (new Error())->handleUnknownError();
    }


    /**
     * Calls appropriate controller class to deal with URL.
     * @return void
     */
    public function loadController(): void
    {
        set_exception_handler(array($this, "exception_handler"));

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
                    // Display error page
                    (new Error())->handlePageNotFoundError();
                }
        }
    }
}