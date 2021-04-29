<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DomesticDeliveryRequest;
use App\Models\DomesticDelivery;
use App\Models\User;
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
            ->when(auth()->user()->company_id, function ($q) {
                return $q->where('domestic_deliveries.company_id', auth()->user()->company_id);
            })->when(auth()->user()->customer_id, function ($q) {
                return $q->where('domestic_deliveries.customer_id', auth()->user()->customer_id);
            })->when(auth()->user()->agent_id, function ($q) {
                return $q->where('domestic_deliveries.agent_id', auth()->user()->agent_id);
            })->when($request->status, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $status = array_filter($request->status, function ($s) {
                        return $s != "null";
                    });

                    $q->when(count($status) > 0, function ($q) use ($status) {
                        $q->whereIn('delivery_status_id', $status);
                    })->when(in_array("null", $request->status), function ($q) {
                        $q->orWhereNull('delivery_status_id');
                    });
                });
            })->when($request->customer_id, function ($q) use ($request) {
                if (is_array($request->customer_id)) {
                    return $q->whereIn('domestic_deliveries.customer_id', $request->customer_id);
                }
                return $q->where('domestic_deliveries.customer_id', $request->customer_id);
            })->when($request->agent_id, function ($q) use ($request) {
                return $q->whereIn('domestic_deliveries.agent_id', $request->agent_id);
            })->when($request->keyword, function ($q) use ($request) {
                return $q->where(function ($qq) use ($request) {
                    return $qq->where('spb_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('resi_number', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('users.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('origin', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('destination', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('delivery_address', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('customers.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('vehicle_types.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('agents.name', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhere('service_type', 'LIKE', '%' . $request->keyword . '%');
                });
            })->when($request->dateRange, function ($q) use ($request) {
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
        $domesticDelivery = DB::transaction(function () use ($request) {
            $domesticDelivery = DomesticDelivery::create(array_merge($request->all(), [
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
            ]));

            $domesticDelivery->items()->createMany(array_map(function ($item) {
                $item['volume'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 1000000;
                $item['volume_weight'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 4000;
                $item['invoice_weight'] = $item['weight'] > $item['volume_weight'] ? $item['weight'] : $item['volume_weight'];
                return $item;
            }, $request->items));

            if ($request->delivery_status_id !== null) {
                $domesticDelivery->progress()->create([
                    'status' => 0, // registered
                    'note' => $request->status_note,
                    'user_id' => $request->user()->id
                ]);
            }


            return $domesticDelivery;
        });

        return ['message' => 'Data telah disimpan', 'data' => $domesticDelivery];
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
        $newData = DB::transaction(function () use ($request, $domesticDelivery) {
            $oldStatus = $domesticDelivery->delivery_status_id;
            $domesticDelivery->update($request->all());
            $domesticDelivery->items()->delete();

            $domesticDelivery->items()->createMany(array_map(function ($item) {
                $item['volume'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 1000000;
                $item['volume_weight'] = $item['dimension_p'] * $item['dimension_l'] * $item['dimension_t'] / 4000;
                $item['invoice_weight'] = $item['weight'] > $item['volume_weight'] ? $item['weight'] : $item['volume_weight'];
                return $item;
            }, $request->items));

            if ($request->delivery_status_id !== null && $oldStatus === null) {
                $domesticDelivery->progress()->create([
                    'status' => 0, // registered
                    'note' => $request->status_note,
                    'user_id' => $request->user()->id
                ]);
            }

            return $domesticDelivery;
        });

        return ['message' => 'Data telah disimpan', 'data' => $newData];
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
        $domesticDelivery->items()->delete();
        $domesticDelivery->progress()->delete();
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

    // untuk ambil data waktu mau generate invoice
    public function search(Request $request)
    {
        return DomesticDelivery::when($request->customer_id, function ($q) use ($request) {
            return $q->where('customer_id', $request->customer_id);
        })->when($request->company_id, function ($q) use ($request) {
            return $q->where('company_id', $request->company_id);
        })->when($request->delivery_status_id, function ($q) use ($request) {
            return $q->where('delivery_status_id', $request->delivery_status_id);
        })->when($request->service_type, function ($q) use ($request) {
            return $q->where('service_type', $request->service_type);
        })->where('invoice_status', $request->invoice_status)->get();
    }

    public function searchApi(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required'
        ]);

        $data = DomesticDelivery::with(['customer'])
            ->where('company_id', $request->company_id) // cuma user yg ada company-nya yg boleh (company, agent, customer)
            ->when($request->customer_id, function ($q) use ($request) {
                return $q->where('customer_id', $request->customer_id);
            })->when($request->agent_id, function ($q) use ($request) {
                return $q->where('agent_id', $request->agent_id);
            })->when($request->tracking_number, function ($q) use ($request) {
                return $q->where(function ($qq) use ($request) {
                    return $qq->where('spb_number', $request->tracking_number)
                        ->orWhere('resi_number', $request->tracking_number);
                });
            })->when($request->delivery_status_id, function ($q) use ($request) {
                return $q->where('delivery_status_id', $request->delivery_status_id);
            })->first();

        if (!$data) {
            return response(['message' => 'Tidak ada data yang cocok'], 404);
        }

        return $data;
    }

    // untuk cek resi di front end
    public function cekResi(Request $request)
    {
        $request->validate([
            'tracking_number' => ['required', function ($attribute, $value, $fail) {
                $exists = DomesticDelivery::where('resi_number', $value)->orWhere('spb_number', $value)->first();

                if (!$exists) {
                    $fail('Nomor resi atau Nomor SPB tidak terdaftar');
                }
            }],
            'phone_or_email' => ['required', function ($attribute, $value, $fail) {
                $exists = User::where('phone', $value)->orWhere('email', 'LIKE', '%' . $value . '%')->first();

                if (!$exists) {
                    $fail('Email atau No. HP tidak terdaftar');
                }
            }]
        ], [], [
            'tracking_number' => 'Nomor Resi/Nomor SPB',
            'phone_or_email' => 'Nomor HP/Email'
        ]);

        $data = DomesticDelivery::selectRaw('domestic_deliveries.*')
            ->join('customers', 'customers.id', '=', 'domestic_deliveries.customer_id')
            ->join('users', 'users.customer_id', '=', 'customers.id')
            ->where(function ($q) use ($request) {
                return $q->where(function ($qq) use ($request) {
                    return $qq->where('users.phone', $request->phone_or_email)
                        ->orWhere('users.email', $request->phone_or_email);
                });
            })
            ->where(function ($q) use ($request) {
                return $q->where(function ($qq) use ($request) {
                    return $qq->where('domestic_deliveries.spb_number', $request->tracking_number)
                        ->orWhere('domestic_deliveries.resi_number', $request->tracking_number);
                });
            })->first();

        if (!$data) {
            return response(['message' => 'Tidak ada data yang cocok'], 404);
        }

        return $data;
    }

    public function cekResi1(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required'
        ]);

        $data = DomesticDelivery::where(function ($q) use ($request) {
            return $q->where(function ($qq) use ($request) {
                return $qq->where('spb_number', $request->tracking_number)
                    ->orWhere('resi_number', $request->tracking_number);
            });
        })->first();

        if (!$data) {
            return response(['message' => 'Tidak ada data yang cocok'], 404);
        }

        return $data->load(['progress']);
    }
}
