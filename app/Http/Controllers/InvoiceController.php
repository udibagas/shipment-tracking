<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Invoice::paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
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
    public function update(InvoiceRequest $request, Invoice $invoice)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status > 0) {
            return response(['message' => 'Data yang sudah disubmit tidak boleh dihapus'], 500);
        }

        $invoice->delete();
        return ['message' => 'Data berhasil dihapus'];
    }

    public function print(Invoice $invoice)
    {
        return view('print.invoice', ['data' => $invoice]);
    }
}
