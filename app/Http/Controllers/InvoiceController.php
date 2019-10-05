<?php

namespace App\Http\Controllers;

use App\DomesticDelivery;
use App\Invoice;
use App\Http\Requests\InvoiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\InvoiceItem;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = $request->sort ? $request->sort : 'date';
        $order = $request->order == 'ascending' ? 'asc' : 'desc';

        return Invoice::selectRaw('
                invoices.*,
                customers.name AS customer,
                users.name AS user
            ')
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->orderBy($sort, $order)->paginate($request->pageSize);
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
            DB::transaction(function () use ($request) {
                $input = $request->only((new Invoice())->getFillable());
                $input['user_id'] = $request->user()->id;
                $input['company_id'] = $request->user()->company_id;
                $input['created_at'] = $input['updated_at'] = now();

                $id = DB::table('invoices')->insertGetId($input);

                DB::table('invoice_items')->insert(array_map(function($item) use ($id) {
                    $data = array_only($item, (new InvoiceItem())->getFillable());
                    $data['invoice_id'] = $id;
                    $data['description'] = json_encode($item['description']);
                    return $data;
                }, $request->items));

                if ($request->status == 1) {
                    $deliveryIds = array_map(function($item) { return $item['description']['delivery_id']; }, $request->items);
                    DomesticDelivery::whereIn('id', $deliveryIds)->update(['invoice_status' => 1]);
                }

                return Invoice::find($id);
            });
        } catch (\Exception $e) {
            return response(['message' => 'Data gagal disimpan. '.$e->getMessage()], 500);
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
        try {
            DB::transaction(function () use ($request, $invoice) {
                $input = $request->only($invoice->getFillable());
                $input['updated_at'] = now();

                DB::table('invoices')
                    ->where('id', $invoice->id)
                    ->update($input);

                // delete item first
                DB::table('invoice_items')
                    ->where('invoice_id', $invoice->id)
                    ->delete();

                DB::table('invoice_items')->insert(array_map(function($item) use ($invoice) {
                    $data = array_only($item, (new InvoiceItem())->getFillable());
                    $data['invoice_id'] = $invoice->id;
                    $data['description'] = json_encode($item['description']);
                    return $data;
                }, $request->items));

                if ($request->status == 1) {
                    $deliveryIds = array_map(function($item) { return $item['description']['delivery_id']; }, $request->items);
                    DomesticDelivery::whereIn('id', $deliveryIds)->update(['invoice_status' => 1]);
                }

                return $invoice;
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
    public function destroy(Invoice $invoice)
    {
        if ($invoice->status > 0) {
            return response(['message' => 'Data yang sudah disubmit tidak boleh dihapus'], 500);
        }

        $invoice->items()->delete();
        $invoice->delete();
        return ['message' => 'Data berhasil dihapus'];
    }

    public function print(Invoice $invoice)
    {
        return view('print.invoice', ['data' => $invoice]);
    }
}
