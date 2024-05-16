<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Exception;
use Steamy\Core\Controller;
use Steamy\Core\Utility;

/**
 * Router for API. It is called for URLs of the form `/api/v1/...`
 *
 * E.g., http://localhost/steamy-sips/public/api/products
 */
class API
{
    use Controller;

    private string $resource;

    public function __construct()
    {
        header("Content-Type:application/json");

        $this->resource = Utility::splitURL()[2] ?? "";
    }

    /**
     * Checks if root relative url starts with api/v1
     * @return bool
     */
    private function validateURLFormat(): bool
    {
        return preg_match("/^api\/v1/", $_GET["url"]) > 0;
    }

    public function index(): void
    {
        if (!$this->validateURLFormat()) {
            http_response_code(400);
            die();
        }

        // call appropriate controller to handle resource
        $controllerClassName = 'Steamy\\Controller\\API\\' . ucfirst($this->resource);
        try {
            if (class_exists($controllerClassName)) {
                (new $controllerClassName())->index();
            } else {
                http_response_code(404);
                die();
            }
        } catch (Exception $e) {
            http_response_code(500);

            // Uncomment line below only when testing API
            echo $e->getMessage();

            die();
        }
    }
}
