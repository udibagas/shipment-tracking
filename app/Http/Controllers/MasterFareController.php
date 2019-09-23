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

        return MasterFare::selectRaw('master_fares.*, customers.name AS customer, companies.name AS company')
            ->join('companies', 'companies.id', '=', 'master_fares.company_id')
            ->join('customers', 'customers.id', '=', 'master_fares.customer_id')
            ->when($request->keyword, function ($q) use ($request) {
                return $q->where('customers.name', 'LIKE', '%' . $request->keyword . '%')
                    ->orWhere('master_fares.destination', 'LIKE', '%' . $request->keyword . '%');
            })->when($request->company_id, function ($q) use ($request) {
                return $q->whereIn('company_id', $request->company_id);
            })->when($request->customer_id, function ($q) use ($request) {
                return $q->whereIn('customer_id', $request->customer_id);
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
}
