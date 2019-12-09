<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyBankRequest extends FormRequest
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
            'company_id' => 'required',
            'bank_name' => 'required',
            'bank_branch' => 'required',
            'account_number' => 'required',
            'account_holder' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'company_id' => 'Nama Perusahaan',
            'bank_name' => 'Nama Bank',
            'bank_branch' => 'Nama Cabang',
            'account_number' => 'Nomor Rekening',
            'account_holder' => 'Pemegang Rekening',
        ];
    }
}
