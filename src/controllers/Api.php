<?php

declare(strict_types=1);

namespace Steamy\Controller;

use Steamy\Core\Controller;

/**
 * This controller deals with URLs of the form `ROOT/api/v1/...`
 *
 * E.g., http://localhost/steamy-sips/public/api/products
 */
class Api
{
    use Controller;

    private function validateURLFormat(): bool
    {
        $matches = [];
        preg_match("/^api\/v1\/*/", $_GET["url"], $matches);
        return count($matches) > 0;
    }

    public function __construct()
    {
        header("Content-Type:application/json");
    }

    public function index(): void
    {
        if (!$this->validateURLFormat()) {
            echo "Invalid API URL" . $_GET["url"];
            return;
        }
        echo json_encode("hello world");
    }
}
