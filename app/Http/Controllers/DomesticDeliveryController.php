<?php

namespace App\Http\Controllers;

use App\DeliveryProgress;
use Illuminate\Http\Request;
use App\Http\Requests\DomesticDeliveryRequest;
use App\DomesticDelivery;
use App\DomesticDeliveryItem;
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
                users.name AS user,
                agents.name AS agent
            ')
            ->join('customers', 'customers.id', '=', 'domestic_deliveries.customer_id')
            ->join('delivery_types', 'delivery_types.id', '=', 'domestic_deliveries.delivery_type_id')
            ->join('users', 'users.id', '=', 'domestic_deliveries.user_id')
            ->join('agents', 'agents.id', '=', 'domestic_deliveries.agent_id', 'LEFT')
            ->join('vehicle_types', 'vehicle_types.id', '=', 'domestic_deliveries.vehicle_type_id', 'LEFT')
            ->when(auth()->user()->company_id, function($q) {
                return $q->where('domestic_deliveries.company_id', auth()->user()->company_id);
            })->when(auth()->user()->customer_id, function($q) {
                return $q->where('domestic_deliveries.customer_id', auth()->user()->customer_id);
            })->when(auth()->user()->agent_id, function($q) {
                return $q->where('domestic_deliveries.agent_id', auth()->user()->agent_id);
            })->when($request->status, function($q) use ($request) {
                return $q->whereIn('delivery_status_id', $request->status);
            })->when($request->customer_id, function($q) use ($request) {
                if (is_array($request->customer_id)) {
                    return $q->whereIn('domestic_deliveries.customer_id', $request->customer_id);
                }
                return $q->where('domestic_deliveries.customer_id', $request->customer_id);
            })->when($request->agent_id, function($q) use ($request) {
                return $q->whereIn('domestic_deliveries.agent_id', $request->agent_id);
            })->when($request->keyword, function ($q) use ($request) {
                return $q->where(function($qq) use ($request) {
                    return $qq->where('spb_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('resi_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('users.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('origin', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('destination', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('customers.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('vehicle_types.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('agents.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('service_type', 'LIKE', '%' . $request->keyword . '%');
                });
            })->when($request->dateRange, function($q) use ($request) {
                return $q->whereBetween('pick_up_date', $request->dateRange);
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
                $input['created_at'] = $input['updated_at'] = now();

                $id = DB::table('domestic_deliveries')->insertGetId($input);

                DB::table('domestic_delivery_items')->insert(array_map(function($item) use ($id) {
                    $item['domestic_delivery_id'] = $id;
                    $item['volume'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 1000000;
                    $item['volume_weight'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 4000;
                    $item['invoice_weight'] = $item['weight'] > $item['volume_weight'] ? $item['weight'] : $item['volume_weight'];
                    return $item;
                }, $request->items));

                // create progress
                DeliveryProgress::create([
                    'delivery_id' => $id,
                    'status' => 0, // registered
                    'note' => $request->status_note,
                    'user_id' => $request->user()->id
                ]);

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
                $input = $request->only($domesticDelivery->getFillable());
                $input['updated_at'] = now();

                DB::table('domestic_deliveries')
                    ->where('id', $domesticDelivery->id)
                    ->update($input);

                // delete item first
                DB::table('domestic_delivery_items')
                    ->where('domestic_delivery_id', $domesticDelivery->id)
                    ->delete();

                DB::table('domestic_delivery_items')->insert(array_map(function($item) use ($domesticDelivery) {
                    $deliveryItem = new DomesticDeliveryItem();
                    $data = array_only($item, $deliveryItem->getFillable());
                    $data['domestic_delivery_id'] = $domesticDelivery->id;
                    $item['volume'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 1000000;
                    $item['volume_weight'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 4000;
                    $item['invoice_weight'] = $item['weight'] > $item['volume_weight'] ? $item['weight'] : $item['volume_weight'];
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

    public function printResi(DomesticDelivery $domesticDelivery)
    {
        return view('print.resi', ['data' => $domesticDelivery]);
    }

    public function printAwb(DomesticDelivery $domesticDelivery)
    {
        return view('print.awb', ['data' => $domesticDelivery]);
    }

    // untuk ambil data waktu mau generate invoice & api buat konfirmasi penerimaan
    public function search(Request $request)
    {
        return DomesticDelivery::when($request->customer_id, function($q) use ($request) {
            return $q->where('customer_id', $request->customer_id);
        })->when($request->keyword, function($q) use ($request) {
            return $q->where(function($qq) use ($request) {
                return $qq->where('spb_number', $request->keyword)
                    ->orWhere('resi_number', $request->keyword);
            });
        })->when($request->company_id, function($q) use ($request) {
            return $q->where('company_id', $request->company_id);
        })->when($request->delivery_status_id, function($q) use ($request) {
            return $q->where('delivery_status_id', $request->delivery_status_id);
        })->when($request->service_type, function($q) use ($request) {
            return $q->where('service_type', $request->service_type);
        })->where('invoice_status', $request->invoice_status)->get();
    }
}
