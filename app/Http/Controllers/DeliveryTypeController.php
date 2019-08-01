<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DeliveryType;
use App\Http\Requests\DeliveryTypeRequest;

class DeliveryTypeController extends Controller
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

        return DeliveryType::when($request->keyword, function ($q) use ($request) {
                return $q->where('name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('code', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->keyword . '%');
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
    public function store(DeliveryTypeRequest $request)
    {
        return DeliveryType::create($request->all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeliveryTypeRequest $request, DeliveryType $deliveryType)
    {
        $deliveryType->update($request->all());
        return $deliveryType;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeliveryType $deliveryType)
    {
        $deliveryType->delete();
        return ['message' => 'Data telah dihapus'];
    }
}
