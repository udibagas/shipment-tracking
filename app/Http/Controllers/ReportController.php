<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function leadTime(Request $request)
    {
        if ($request->type == 'domestic')
        {
            $sql = "SELECT
                MONTH(pick_up_date) AS `month`,
                CAST(SUM(CASE WHEN delivered_date <= eta THEN 1 ELSE 0 END) AS UNSIGNED) AS `ontime`,
                CAST(SUM(CASE WHEN delivered_date > eta THEN 1 ELSE 0 END) AS UNSIGNED) AS `delay`,
                CAST(SUM(CASE WHEN delivered_date IS NOT NULL THEN 1 ELSE 0 END) AS UNSIGNED) AS `total`
            FROM domestic_deliveries
            WHERE customer_id = :customer_id
                AND YEAR(pick_up_date) = :year
            GROUP BY MONTH(pick_up_date)
            ";

            $params = [
                ':year' => $request->year,
                'customer_id' => $request->customer_id
            ];

            return DB::select($sql, $params);
        }
    }

    public function summary(Request $request)
    {
        $sql = "SELECT
                customers.name AS customer,
                SUM(CASE WHEN delivery_status_id = 0 THEN 1 ELSE 0 END) AS registered,
                SUM(CASE WHEN delivery_status_id = 1 THEN 1 ELSE 0 END) AS ready_for_delivery,
                SUM(CASE WHEN delivery_status_id = 2 THEN 1 ELSE 0 END) AS on_delivery,
                SUM(CASE WHEN delivery_status_id = 3 THEN 1 ELSE 0 END) AS delivered,
                SUM(CASE WHEN delivery_status_id = 4 THEN 1 ELSE 0 END) AS received,
                COUNT(domestic_deliveries.id) AS `total`
            FROM domestic_deliveries
            JOIN customers ON customers.id = domestic_deliveries.customer_id
            WHERE company_id = :company_id
            GROUP BY customers.name
        ";

        return DB::select($sql, [':company_id' => $request->user()->company_id]);
    }

    public function getFilterYear()
    {
        return DB::select("SELECT DISTINCT(YEAR(delivered_date)) AS `year` FROM domestic_deliveries ORDER BY `year` ASC");
    }
}
