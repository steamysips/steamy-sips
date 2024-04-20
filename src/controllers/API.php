<?php

declare(strict_types=1);

namespace Steamy\Controller;

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
     * Checks if root relative url starts with /api/v1
     * @return bool
     */
    private function validateURLFormat(): bool
    {
        return preg_match("/^api\/v1/", $_GET["url"]) > 0;
    }

    public function index(): void
    {
        if (!$this->validateURLFormat()) {
            echo "Invalid API URL: " . $_GET["url"];
            die();
        }

        // call appropriate controller to handle resource
        $controllerClassName = 'Steamy\\Controller\\API\\' . ucfirst($this->resource);

        if (class_exists($controllerClassName)) {
            (new $controllerClassName())->index();
        } else {
            echo "Invalid API resource: " . $this->resource;
            die();
        }
    }
}
