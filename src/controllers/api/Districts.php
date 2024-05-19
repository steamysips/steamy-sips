<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Steamy\Core\Utility;
use Steamy\Model\District;
use Steamy\Core\Model;

class Districts
{
    use Model;

    /**
     * Get the list of all districts available.
     */
    private function getAllDistricts(): void
    {
        // Retrieve all districts from the database
        $allDistricts = District::getAll();

        // Convert districts to array format
        $result = [];
        foreach ($allDistricts as $district) {
            $result[] = [
                'district_id' => $district->getID(),
                'name' => $district->getName()
            ];
        }

        // Return JSON response
        echo json_encode($result);
    }

    /**
     * Get the details of a specific district by its ID.
     */
    private function getDistrictById(): void
    {
        $districtId = (int)Utility::splitURL()[3];

        // Retrieve district details from the database
        $district = District::getByID($districtId);

        // Check if district exists
        if ($district === null) {
            // District not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'District not found']);
            return;
        }

        // Return JSON response
        echo json_encode([
            'district_id' => $district->getID(),
            'name' => $district->getName()
        ]);
    }

    private function getHandler($routes): ?string
    {
        foreach ($routes[$_SERVER['REQUEST_METHOD']] as $route => $handler) {
            $pattern = str_replace('/', '\/', $route); // Convert to regex pattern
            $pattern = preg_replace(
                '/\{([a-zA-Z0-9_]+)\}/',
                '(?P<$1>[^\/]+)',
                $pattern
            ); // Replace placeholders with regex capture groups
            $pattern = '/^' . $pattern . '$/';

            if (preg_match($pattern, '/' . Utility::getURL(), $matches)) {
                return $handler;
            }
        }
        return null;
    }

    /**
     * Main entry point for the Districts API.
     */
    public function index(): void
    {
        $routes = [
            'GET' => [
                '/api/v1/districts' => 'getAllDistricts',
                '/api/v1/districts/{id}' => 'getDistrictById',
            ]
        ];

        // Handle the request
        $handler = $this->getHandler($routes);

        if ($handler !== null) {
            $functionName = $handler;
            if (method_exists($this, $functionName)) {
                call_user_func(array($this, $functionName));
            } else {
                // Handle function not found
                http_response_code(404);
                echo "Function Not Found";
                die();
            }
        } else {
            // Handle route not found
            http_response_code(404);
            echo "Route Not Found";
            die();
        }
    }
}
