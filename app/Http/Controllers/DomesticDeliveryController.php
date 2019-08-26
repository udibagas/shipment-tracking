<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DomesticDeliveryRequest;
use App\DomesticDelivery;

class DomesticDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'updated_at';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return DomesticDelivery::selectRaw('
                domestic_deliveries.*,
                customers.name AS customer,
                delivery_types.name AS delivery_type,
                service_types.name AS service_type,
                users.name AS user,
                agents.name AS agent
            ')
            ->join('customers', 'customers.id', '=', 'domestic_deliveries.customer_id')
            ->join('delivery_types', 'delivery_types.id', '=', 'domestic_deliveries.delivery_type_id')
            ->join('service_types', 'service_types.id', '=', 'domestic_deliveries.service_type_id')
            ->join('users', 'users.id', '=', 'domestic_deliveries.user_id')
            ->join('agents', 'agents.id', '=', 'domestic_deliveries.agent_id', 'LEFT')
            ->when($request->status, function($q) use ($request) {
                return $q->whereIn('delivery_status_id', $request->status);
            })
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('spb_number', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('resi_number', 'LIKE', '%' . $request->keyword . '%');
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomesticDeliveryRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $input['company_id'] = $request->user()->company_id;
        $input['delivery_status_id'] = DomesticDelivery::STATUS_REGISTERED;
        return DomesticDelivery::create($input);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DomesticDelivery $domesticDelivery)
    {
        return $domesticDelivery;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DomesticDeliveryRequest $request, DomesticDelivery $domesticDelivery)
    {
        $domesticDelivery->update($request->all());
        return $domesticDelivery;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomesticDelivery $domesticDelivery)
    {
        $domesticDelivery->delete();
        return ['message' => 'Data telah dihapus'];
    }
}
