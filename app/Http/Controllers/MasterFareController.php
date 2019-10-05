<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterFareRequest;
use App\MasterFare;
use Illuminate\Http\Request;

class MasterFareController extends Controller
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

        return MasterFare::selectRaw('
                master_fares.*,
                customers.name AS customer,
                companies.name AS company,
                vehicle_types.name AS vehicle
            ')
            ->join('companies', 'companies.id', '=', 'master_fares.company_id')
            ->join('customers', 'customers.id', '=', 'master_fares.customer_id')
            ->join('vehicle_types', 'vehicle_types.id', '=', 'master_fares.vehicle_type_id', 'LEFT')
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('customers.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('master_fares.destination', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('vehicle_types.name', 'LIKE', '%' . $request->keyword . '%');
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
    public function store(MasterFareRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $input['company_id'] = $request->user()->company_id;
        return MasterFare::create($input);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasterFareRequest $request, MasterFare $masterFare)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $masterFare->update($input);
        return $masterFare;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterFare $masterFare)
    {
        $masterFare->delete();
        return ['message' => 'Data berhasil dihapus.'];
    }

    public function search(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'customer_id' => 'required',
            'destination' => 'required',
        ]);

        $fare = MasterFare::where('company_id', $request->company_id)
            ->where('customer_id', $request->customer_id)
            ->where('destination', $request->destination)
            ->first();

        if (!$fare) {
            return response(['message' => 'Tidak ada tarif untuk data dimaksud'], 404);
        }

        return $fare;
    }
}
