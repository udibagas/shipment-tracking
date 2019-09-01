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
}
