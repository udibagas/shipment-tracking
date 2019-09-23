<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterFareCharterRequest;
use App\Http\Requests\MasterFareRequest;
use App\MasterFareCharter;
use Illuminate\Http\Request;

class MasterFareCharterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'customer';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return MasterFareCharter::selectRaw('master_fare_charters.*, customers.name AS customer, companies.name AS company')
            ->join('companies', 'companies.id', '=', 'master_fare_charters.company_id')
            ->join('customers', 'customers.id', '=', 'master_fare_charters.customer_id')
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('customers.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('master_fare_charters.destination', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->company_id, function ($q) use ($request) {
                return $q->whereIn('company_id', $request->company_id);
            })->when($request->customer_id, function ($q) use ($request) {
                return $q->whereIn('customer_id', $request->customer_id);
            })->when($request->vehicle_type_id, function ($q) use ($request) {
                return $q->whereIn('vehicle_type_id', $request->vehicle_type_id);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasterFareCharterRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $input['company_id'] = $request->user()->company_id;
        return MasterFareCharter::create($input);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasterFareCharterRequest $request, MasterFareCharter $masterFareCharter)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $masterFareCharter->update($input);
        return $masterFareCharter;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterFareCharter $masterFareCharter)
    {
        $masterFareCharter->delete();
        return ['message' => 'Data berhasil dihapus.'];
    }
}
