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

        return DomesticDelivery::when($request->keyword, function ($q) use ($request) {
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
