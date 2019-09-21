<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DomesticDeliveryRequest;
use App\DomesticDelivery;
use Illuminate\Support\Facades\DB;

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
            })->when($request->customer_id, function($q) use ($request) {
                return $q->whereIn('domestic_deliveries.customer_id', $request->customer_id);
            })->when($request->agent_id, function($q) use ($request) {
                return $q->whereIn('domestic_deliveries.agent_id', $request->agent_id);
            })->when($request->keyword, function ($q) use ($request) {
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
        $domesticDelivery = new DomesticDelivery();

        try {
            DB::transaction(function () use ($request, $domesticDelivery) {
                $input = $request->only($domesticDelivery->getFillable());
                $input['user_id'] = $request->user()->id;
                $input['company_id'] = $request->user()->company_id;
                $input['delivery_status_id'] = DomesticDelivery::STATUS_REGISTERED;

                $id = DB::table('domestic_deliveries')->insertGetId($input);

                DB::table('domestic_delivery_items')->insert(array_map(function($item) use ($id) {
                    $item['domestic_delivery_id'] = $id;
                    return $item;
                }, $request->items));

                return DomesticDelivery::find($id);
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '.$e->getMessage()], 500);
        }
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
        try {
            DB::transaction(function () use ($request, $domesticDelivery) {
                DB::table('domestic_deliveries')
                    ->where('id', $domesticDelivery->id)
                    ->update($request->only($domesticDelivery->getFillable()));

                // delete item first
                DB::table('domestic_delivery_items')
                    ->where('domestic_delivery_id', $domesticDelivery->id)
                    ->delete();

                DB::table('domestic_delivery_items')->insert(array_map(function($item) use ($domesticDelivery) {
                    $data = array_only($item, ['description', 'coli', 'weight', 'item', 'reference', 'remark']);
                    $data['domestic_delivery_id'] = $domesticDelivery->id;
                    return $data;
                }, $request->items));

                return $domesticDelivery;
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '.$e->getMessage()], 500);
        }
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

    public function printSpb(DomesticDelivery $domesticDelivery)
    {
        return view('print.spb', ['data' => $domesticDelivery]);
    }

    public function printResi(DomesticDelivery $domesticDelivery)
    {
        return view('print.resi', ['data' => $domesticDelivery]);
    }

    public function printAwb(DomesticDelivery $domesticDelivery)
    {
        return view('print.awb', ['data' => $domesticDelivery]);
    }
}
