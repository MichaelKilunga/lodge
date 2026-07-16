<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->user->role == 'Customer') {
            return [
                'name' => 'required',
                'email' => 'required|unique:users,email,'.$this->user->id,
                'phone' => 'nullable|string',
            ];
        }

        return [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$this->user->id,
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ];
    }

}
