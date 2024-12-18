<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterFareRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => 'required',
            'destination' => 'required',
            'fare' => 'required|numeric',
            'lead_time' => 'required|numeric',
            'minimum' => 'required|numeric',
            'vehicle_type_id' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'customer_id' => 'Customer',
            'destination' => 'Tujuan',
            'fare' => 'Tarif',
            'lead_time' => 'Lead Time',
            'minimum' => 'Min. Charge',
            'vehicle_type_id' => 'Jenis Armada'
        ];
    }
}
