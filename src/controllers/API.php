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

    public static string $API_BASE_URI = '/api/v1'; // root-relative

    private string $resource;

    public function __construct()
    {
        // Set the Content-Type header to application/json
        header("Content-Type:application/json");

        // Allow access from any origin to avoid CORS issues
        $this->cors();

        $this->resource = Utility::splitURL()[2] ?? "";
    }

    /**
     *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
     *  origin.
     *
     *  In a production environment, you probably want to be more restrictive, but this gives you
     *  the general idea of what is involved.  For the nitty-gritty low-down, read:
     *
     *  - https://developer.mozilla.org/en/HTTP_access_control
     *  - https://fetch.spec.whatwg.org/#http-cors-protocol
     *    Reference: https://stackoverflow.com/a/9866124/17627866
     */
    private function cors(): void
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) // may also be using PUT, PATCH, HEAD etc
            {
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
        }
    }

    /**
     * Checks if root relative url starts with api/v1
     * @return bool
     */
    private function validateURLFormat(): bool
    {
        return preg_match("/^api\/v1/", Utility::getURL()) > 0;
    }


    /**
     * Returns the name of function responsible for handling the current request, as defined by the $routes variable.
     * @param string $controllerName class name of controller
     * @return string|null
     */
    private function getHandler(string $controllerName): ?string
    {
        $all_routes = $controllerName::$routes;

        // check if there are handlers defined for current request method
        $my_routes = $all_routes[$_SERVER['REQUEST_METHOD']] ?? "";
        if (empty($my_routes)) {
            return null;
        }

        foreach ($my_routes as $route => $handler) {
            $route = API::$API_BASE_URI . $route;
            $pattern = str_replace('/', '\/', $route); // Convert to regex pattern
            $pattern = preg_replace(
                '/\{([a-zA-Z0-9_]+)\}/',
                '(?P<$1>[^\/]+)',
                $pattern
            ); // Replace placeholders with regex capture groups
            $pattern = '/^' . $pattern . '$/';

            if (preg_match($pattern, '/' . Utility::getURL())) {
                return $handler;
            }
        }
        return null;
    }

    public function index(): void
    {
        if (!$this->validateURLFormat()) {
            http_response_code(400);
            return;
        }

        // check if there is a controller to handle resource
        $controllerClassName = 'Steamy\\Controller\\API\\' . ucfirst($this->resource);
        if (!class_exists($controllerClassName)) {
            // no controller available
            http_response_code(404);
            echo 'Invalid resource: ' . $this->resource; // comment this line for production
            return;
        }

        // determine which function to call in the controller to handle route
        $functionName = $this->getHandler($controllerClassName);
        if ($functionName === null) {
            // Controller does not have any method defined for route
            http_response_code(404);
            echo "Request has not been defined in \$routes for " . $controllerClassName;
            return;
        }

        $controller = new $controllerClassName();

        if (!method_exists($controller, $functionName)) {
            // handle function not found in controller
            http_response_code(500);
            echo $controllerClassName . ' does not have a public method ' . $functionName;
            return;
        }

        // call function in controller for handling request
        try {
            call_user_func(array($controller, $functionName));
        } catch (Exception $e) {
            http_response_code(500);

            // Uncomment line below only when testing API
            echo $e->getMessage();
        }
    }
}
