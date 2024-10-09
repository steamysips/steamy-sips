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
     * Each object includes:
     * - The month (first day of the month).
     * - The total number of orders for that month.
     * - The total revenue for that month.
     * - The percentage difference in total orders from the previous month.
     * - The percentage difference in total revenue from the previous month.
     *
     * Example response:
     * <pre>
     *     [
     *         {
     *             "month": "2024-06-01",
     *             "totalOrders": 1,
     *             "totalRevenue": "3.49",
     *             "percentageDifferenceOrders": null,
     *             "percentageDifferenceRevenue": null
     *         },
     *         {
     *             "month": "2024-07-01",
     *             "totalOrders": 9,
     *             "totalRevenue": "40.91",
     *             "percentageDifferenceOrders": "800.00",
     *             "percentageDifferenceRevenue": "1072.21"
     *         },
     *         {
     *             "month": "2024-08-01",
     *             "totalOrders": 1,
     *             "totalRevenue": "7.98",
     *             "percentageDifferenceOrders": "-88.89",
     *             "percentageDifferenceRevenue": "-80.49"
     *         },
     *         {
     *             "month": "2024-10-01",
     *             "totalOrders": 1,
     *             "totalRevenue": "7.98",
     *             "percentageDifferenceOrders": "0.00",
     *             "percentageDifferenceRevenue": "0.00"
     *         }
     *     ]
     * </pre>
     *
     * @return void
     */
    public function getSalesOverTime(): void
    {
        $query = <<< EOL
        WITH monthly_stats AS (
            SELECT
                DATE_FORMAT(o.created_date, '%Y-%m-01') AS month,  -- Group by month
                COUNT(DISTINCT o.order_id) AS totalOrders,        -- Count the number of unique orders
                SUM(op.quantity * op.unit_price) AS totalRevenue  -- Total revenue calculation
            FROM
                `order` o
            JOIN
                order_product op ON o.order_id = op.order_id
            GROUP BY
                DATE_FORMAT(o.created_date, '%Y-%m-01')            -- Group by the first day of each month
        ),
        monthly_diff AS (
            SELECT
                month,
                totalOrders,
                totalRevenue,
                LAG(totalOrders) OVER (ORDER BY month) AS previousMonthOrders,   -- Get previous month's totalOrders
                LAG(totalRevenue) OVER (ORDER BY month) AS previousMonthRevenue   -- Get previous month's totalRevenue
            FROM
                monthly_stats
        )
        SELECT
            month,
            totalOrders,
            totalRevenue,
            CASE
                WHEN previousMonthOrders IS NOT NULL AND previousMonthOrders != 0 THEN
                    ROUND(((totalOrders - previousMonthOrders) * 100.0 / previousMonthOrders), 2)
                ELSE
                    NULL  -- No previous month data for the first row
            END AS percentageDifferenceOrders,
            CASE
                WHEN previousMonthRevenue IS NOT NULL AND previousMonthRevenue != 0 THEN
                    ROUND(((totalRevenue - previousMonthRevenue) * 100.0 / previousMonthRevenue), 2)
                ELSE
                    NULL  -- No previous month data for the first row
            END AS percentageDifferenceRevenue
        FROM
            monthly_diff
        ORDER BY
            month;  -- Order by month
        EOL;

        $con = self::connect();
        $stm = $con->prepare($query);
        $stm->execute();

        echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
    }
}