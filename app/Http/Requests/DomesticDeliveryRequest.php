<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DomesticDeliveryRequest extends FormRequest
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
            'company_id' => 'exists:companies,id',
            'customer_id' => 'required|exists:customers,id',
            'agent_id' => 'exists:agents,id',
            'service_type_id' => 'required|exists:service_types,id',
            'origin' => 'required|exists:cities,name',
            'destination' => 'required|exists:cities,name',
            // 'delivery_status_id' => 'required|exists:delivery_statuses,id',
            'pick_up_date' => 'required|date',
            'spb_number' => 'required',
            'resi_number' => 'required',
            'volume' => 'numeric',
            'quantity' => 'numeric',
            'delivery_type_id' => 'required|exists:delivery_types,id',
            // 'etd' => 'required|date',
            // 'eta' => 'required|date',
            'charge_to' => 'required',
            'delivery_address' => 'required'
        ];
    }
}
