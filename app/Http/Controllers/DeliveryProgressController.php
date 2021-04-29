<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryProgress;
use App\Models\DomesticDelivery;
use App\Http\Requests\DeliveryProgressRequest;
use Illuminate\Support\Facades\DB;

class DeliveryProgressController extends Controller
{
    public function index(Request $request)
    {
        return DeliveryProgress::selectRaw('delivery_progresses.*, users.name AS user')
            ->join('users', 'users.id', '=', 'delivery_progresses.user_id')
            ->when($request->delivery_id, function ($q) use ($request) {
                return $q->where('delivery_id', $request->delivery_id);
            })->orderBy('created_at', 'ASC')->get();
    }

    public function store(DeliveryProgressRequest $request)
    {
        $data = DB::transaction(function () use ($request) {
            DomesticDelivery::where('id', $request->delivery_id)->update(
                array_merge(
                    $request->only((new DomesticDelivery())->getFillable()),
                    [
                        'delivery_status_id' => $request->status,
                        'status_note' => $request->note
                    ]
                )
            );

            return DeliveryProgress::create(
                array_merge(
                    $request->all(),
                    ['user_id' => $request->user()->id]
                )
            );
        });

        return $data;
    }

    public function update(DeliveryProgressRequest $request, DeliveryProgress $deliveryProgress)
    {
        $data = DB::transaction(function () use ($request, $deliveryProgress) {
            DomesticDelivery::where('id', $request->delivery_id)->update(
                array_merge(
                    $request->only((new DomesticDelivery())->getFillable()),
                    [
                        'delivery_status_id' => $request->status,
                        'status_note' => $request->note
                    ]
                )
            );

            $deliveryProgress->update($request->all());
            return $deliveryProgress;
        });

        return $data;
    }
}
