<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$this->id,
            'password' => 'sometimes|required|alpha_num|confirmed|min:6',
            'is_active' => 'boolean',
            'role' => 'required|numeric|in:11,21,31,41,51',
            'company_id' => 'required_if:role,21,31|exists:companies,id',
            // 'customer_id' => 'required_if:role,41|exists:customers,id',
            // 'agent_id' => 'required_if:role,51|exists:agents,id'
        ];
    }
}
