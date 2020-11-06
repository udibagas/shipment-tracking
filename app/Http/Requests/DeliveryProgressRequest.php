<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryProgressRequest extends FormRequest
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
            'status' => 'required|in:1,2,3,4,5,6',
            'etd' => 'required_if:status,1|date',
            // 'tracking_number' => 'required_if:status,1',
            'agent_id' => 'required_if:status,1|exists:agents,id',
            'delivery_date' => 'required_if:status,2|date',
            'eta' => 'required_if:status,2|date',
            // 'delivered_date' => 'required_if:status,3|date',
            'stt_received_date' => 'required_if:status,4|date',
            'receiver' => 'required_if:status,3',
            'invoice_date' => 'required_if:status,5|date',
            'payment_date' => 'required_if:status,6|date',
        ];
    }
}
