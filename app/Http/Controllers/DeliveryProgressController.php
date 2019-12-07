<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeliveryProgress;
use App\DomesticDelivery;
use App\Mail\ShipmentDelivered;
use Illuminate\Support\Facades\Mail;
use App\Company;
use Illuminate\Support\Facades\Config;

class DeliveryProgressController extends Controller
{
    public function index(Request $request)
    {
        return DeliveryProgress::selectRaw('delivery_progresses.*, users.name AS user')
        ->join('users', 'users.id', '=', 'delivery_progresses.user_id')
        ->when($request->delivery_id, function($q) use ($request) {
            return $q->where('delivery_id', $request->delivery_id);
        })->orderBy('created_at', 'ASC')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|in:1,2,3,4,5,6',
            'etd' => 'required_if:status,1|date',
            // 'tracking_number' => 'required_if:status,1',
            'agent_id' => 'required_if:status,1|exists:agents,id',
            'delivery_date' => 'required_if:status,2|date',
            'eta' => 'required_if:status,2|date',
            'delivered_date' => 'required_if:status,3|date',
            'received_date' => 'required_if:status,4|date',
            'receiver' => 'required_if:status,3,4',
            'invoice_date' => 'required_if:status,5|date',
            'payment_date' => 'required_if:status,6|date',
        ]);

        $input = $request->only((new DomesticDelivery())->getFillable());
        $input['delivery_status_id'] = $request->status;
        $input['status_note'] = $request->note;
        $delivery = DomesticDelivery::where('id', $request->delivery_id)->first();
        $delivery->update($input);

        if ($request->status == DomesticDelivery::STATUS_DELIVERED)
        {
            $company = Company::find($request->user()->company_id);

            if ($company)
            {
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

                Mail::to(explode(', ', $delivery->customer->email))
                    ->cc($request->user()->email)
                    ->send(new ShipmentDelivered($delivery));
            }
        }

        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        return DeliveryProgress::create($input);
    }
}
