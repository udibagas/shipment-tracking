<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterFarePackingRequest;
use App\Models\MasterFarePacking;
use Illuminate\Http\Request;

class MasterFarePackingController extends Controller
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

        return MasterFarePacking::selectRaw('master_fare_packings.*, customers.name AS customer, companies.name AS company')
            ->join('companies', 'companies.id', '=', 'master_fare_packings.company_id')
            ->join('customers', 'customers.id', '=', 'master_fare_packings.customer_id')
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('customers.name', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->company_id, function ($q) use ($request) {
                return $q->whereIn('master_fare_packings.company_id', $request->company_id);
            })->when($request->customer_id, function ($q) use ($request) {
                return $q->whereIn('master_fare_packings.customer_id', $request->customer_id);
            })->orderBy($sort, $order)->paginate($request->pageSize);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MasterFarePackingRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $input['company_id'] = $request->user()->company_id;
        return MasterFarePacking::create($input);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MasterFarePackingRequest $request, MasterFarePacking $masterFarePacking)
    {
        $input = $request->all();
        $input['user_id'] = $request->user()->id;
        $input['company_id'] = $request->user()->company_id;
        $masterFarePacking->update($input);
        return $masterFarePacking;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MasterFarePacking $masterFarePacking)
    {
        $masterFarePacking->delete();
        return ['message' => 'Data berhasil dihapus.'];
    }

    public function search(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'customer_id' => 'required',
        ]);

        $fare = MasterFarePacking::where('company_id', $request->company_id)
            ->where('customer_id', $request->customer_id)
            ->first();

        if (!$fare) {
            return response(['message' => 'Tidak ada tarif untuk data dimaksud'], 404);
        }

        return $fare;
    }
}
