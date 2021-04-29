<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryStatus;
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
        $data = DeliveryStatus::when($request->keyword, function ($q) use ($request) {
            return $q->where('name', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('code', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('phone', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('address', 'LIKE', '%' . $request->keyword . '%')
                ->orWhere('email', 'LIKE', '%' . $request->keyword . '%');
        })->when($request->status, function ($q) use ($request) {
            return $q->whereIn('status', $request->status);
        })->orderBy($request->sort ?: 'name', $request->order ?: 'asc');

        return $request->paginated ? $data->paginate($request->per_page) : $data->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeliveryStatusRequest $request)
    {
        DeliveryStatus::create($request->all());
        return ['message' => 'Data telah disimpan'];
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
        return ['message' => 'Data telah diupdate'];
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
