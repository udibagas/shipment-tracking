<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            'date' => 'required|date',
            'number' => 'required|unique:invoices,number,'.$this->id,
            'faktur' => 'required|unique:invoices,faktur,'.$this->id,
            'customer_id' => 'required|exists:customers,id',
            'total' => 'required|numeric',
            'total_said' => 'required',
            'service_type' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'date' => 'Tanggal Invoice',
            'number' => 'Nomor Invoice',
            'faktur' => 'Nomor Faktur',
            'customer_id' => 'Customer',
            'total' => 'Total',
            'total_said' => 'Terbilang',
            'service_type' => 'Jenis Layanan'
        ];
    }
}
