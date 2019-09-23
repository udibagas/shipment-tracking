<?php

namespace App\Http\Controllers;

use App\DomesticDeliveryInvoice;
use App\Http\Requests\DomesticDeliveryInvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomesticDeliveryInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return DomesticDeliveryInvoice::paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomesticDeliveryInvoiceRequest $request)
    {
        try {
            DB::transaction(function () {
                $id = DB::table('domestic_delivery_invoices')->insertGetId([]);
            });
        } catch (\Exception $e) {
            return response(['message' => 'Gagal menyimpan data. '.$e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DomesticDeliveryInvoiceRequest $request, DomesticDeliveryInvoice $domesticDeliveryInvoice)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomesticDeliveryInvoice $domesticDeliveryInvoice)
    {
        if ($domesticDeliveryInvoice->status > 0) {
            return response(['message' => 'Data yang sudah disubmit tidak boleh dihapus'], 500);
        }

        $domesticDeliveryInvoice->delete();
        return ['message' => 'Data berhasil dihapus'];
    }

    public function print(DomesticDeliveryInvoice $domesticDeliveryInvoice)
    {
        return view('print.invoice', ['data' => $domesticDeliveryInvoice]);
    }
}
