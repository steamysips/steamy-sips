<?php

declare(strict_types=1);

namespace Steamy\Controller\API;

use PDO;
use Steamy\Core\Model;

class Orders
{
    use Model;

    public static array $routes = [
        'GET' => [
            '/orders' => 'getAllOrders',
            '/orders/stats/sales-over-time' => 'getSalesOverTime',
        ]
    ];

    /**
     * Get all orders
     * @return void
     */
    public function getAllOrders(): void
    {
        $con = self::connect();
        $stm = $con->prepare("SELECT * FROM `order`");
        $stm->execute();

        echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Get total revenue for each month.
     *
     * Example:
     *
     * <pre>
     *     [
     *         {
     *             "date": "2024-06-01",
     *             "totalOrders": 1,
     *             "totalRevenue": "3.49"
     *         },
     *         {
     *             "date": "2024-07-01",
     *             "totalOrders": 9,
     *             "totalRevenue": "40.91"
     *         }
     *     ]
     * </pre>
     *
     * @return void
     */
    public function getSalesOverTime(): void
    {
        $query = <<< EOL
        SELECT
            DATE_FORMAT(o.created_date, '%Y-%m-01') AS date,  -- Group by month
            COUNT(DISTINCT o.order_id) AS totalOrders,        -- Count the number of unique orders
            SUM(op.quantity * op.unit_price) AS totalRevenue  -- Total revenue calculation
        FROM
            `order` o
        JOIN
            order_product op ON o.order_id = op.order_id
        GROUP BY
            DATE_FORMAT(o.created_date, '%Y-%m-01')            -- Group by the first day of each month
        ORDER BY
            date;                                            -- Order by date
        EOL;

        $con = self::connect();
        $stm = $con->prepare($query);
        $stm->execute();

        echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    }
}