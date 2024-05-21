<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use Steamy\Core\Utility;
use Steamy\Model\District;
use Steamy\Core\Model;

class Districts
{
    use Model;

    public static array $routes = [
        'GET' => [
            '/api/v1/districts' => 'getAllDistricts',
            '/api/v1/districts/{id}' => 'getDistrictById',
        ]
    ];

    /**
     * Get the list of all districts available.
     */
    public function getAllDistricts(): void
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
    public function getDistrictById(): void
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
}
