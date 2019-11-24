<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyRequest extends FormRequest
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
            'code' => 'required',
            'name' => 'required',
            'email' => 'email',
            'director_name' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'Kode',
            'name' => 'Nama',
            'email' => 'Email',
            'director_name' => 'Nama Direktur'
        ];
    }
}
