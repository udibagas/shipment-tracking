<?php

namespace App\Http\Controllers;

use App\Company;
use App\Mail\DomesticDeliveryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

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
                SUM(CASE WHEN delivery_status_id = 4 THEN 1 ELSE 0 END) AS stt_received,
                COUNT(domestic_deliveries.id) AS `total`
            FROM domestic_deliveries
            JOIN customers ON customers.id = domestic_deliveries.customer_id
            WHERE domestic_deliveries.company_id = :company_id
                AND pick_up_date BETWEEN :start AND :end
            GROUP BY customers.name
        ";

        return DB::select($sql, [
            ':company_id' => $request->user()->company_id,
            ':start' => $request->dateRange ? $request->dateRange[0] : '1970-01-01',
            ':end' => $request->dateRange ? $request->dateRange[1] : date('Y-m-d'),
        ]);
    }

    public function getFilterYear()
    {
        return DB::select("SELECT DISTINCT(YEAR(delivered_date)) AS `year` FROM domestic_deliveries ORDER BY `year` ASC");
    }

    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'email' => 'required',
            // 'body' => 'required',
            'dateRange' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'array|min:1'
        ], [], [
            'customer_id' => 'Customer',
            'subject' => 'Subyek Pesan',
            'email' => 'Penerima Email',
            'body' => 'Isi Pesan',
            'dateRange' => 'Tanggal',
        ]);

        $company = Company::find($request->user()->company_id);
        Config::set('app.name', $company->name . ' Shipment Tracking');
        Config::set('mail.host', $company->smtp_host);
        Config::set('mail.port', $company->smtp_port);
        Config::set('mail.from', [
            'address' => $company->smtp_username,
            'name' => $company->name,
        ]);
        Config::set('mail.username', $company->smtp_username);
        Config::set('mail.password', $company->smtp_password);
        Config::set('mail.encryption', $company->smtp_encryption);

        // return new DomesticDeliveryReport($request);

        Mail::to(explode(', ', $request->email))->send(new DomesticDeliveryReport($request));
        return ['message' => 'Email berhasil dikirim'];
    }
}
