<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeliveryStatus;
use App\Http\Requests\DeliveryStatusRequest;

class DeliveryStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'name';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return DeliveryStatus::when($request->keyword, function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('code', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('address', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->status, function ($q) use ($request) {
                return $q->whereIn('status', $request->status);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryStatusRequest $request)
    {
        return DeliveryStatus::create($request->all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryStatusRequest $request, DeliveryStatus $deliveryStatus)
    {
        $deliveryStatus->update($request->all());
        return $deliveryStatus;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryStatus $deliveryStatus)
    {
        $deliveryStatus->delete();
        return ['message' => 'Data telah dihapus'];
    }
}
