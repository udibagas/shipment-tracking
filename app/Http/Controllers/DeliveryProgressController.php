<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeliveryProgress;
use App\DomesticDelivery;

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

        $input = $request->except(['status', 'delivery_id', 'note']);
        $input['delivery_status_id'] = $request->status;
        $input['status_note'] = $request->note;
        DomesticDelivery::where('id', $request->delivery_id)->update($input);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        return DeliveryProgress::create($input);
    }
}
