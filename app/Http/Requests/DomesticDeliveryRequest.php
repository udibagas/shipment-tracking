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
            'service_type' => 'required',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'origin' => 'required|exists:cities,name',
            'destination' => 'required|exists:cities,name',
            'pick_up_date' => 'required|date',
            'spb_number' => 'required',
            'resi_number' => 'required',
            'volume' => 'numeric',
            'weight' => 'numeric',
            'quantity' => 'numeric',
            'delivery_type_id' => 'required|exists:delivery_types,id',
            'delivery_address' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'company_id' => 'Company',
            'customer_id' => 'Customer',
            'agent_id' => 'Agent',
            'service_type_id' => 'Service Type',
            'vehicle_type_id' => 'Jenis Armada',
            'origin' => 'Origin',
            'destination' => 'Destination',
            'pick_up_date' => 'Pic Up Date',
            'spb_number' => 'SPN Number',
            'resi_number' => 'Receipt Number',
            'volume' => 'Volume',
            'weight' => 'Weight',
            'quantity' => 'Quantity',
            'delivery_type_id' => 'Delivery Type',
            'etd' => 'ETD',
            'eta' => 'ETA',
            'delivery_address' => 'Delive Address',
        ];
    }
}
