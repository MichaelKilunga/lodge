<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'avatar' => 'nullable|mimes:png,jpg',
        ];

        if ($this->routeIs('transaction.reservation.storeCustomer')) {
            $rules['email'] = 'required|email';
            $rules['phone'] = 'required|string|max:30';
            $rules['address'] = 'nullable|max:255';
            $rules['job'] = 'nullable';
            $rules['birthdate'] = 'nullable|date';
            $rules['gender'] = 'nullable|in:Male,Female';
        } else {
            if ($this->isMethod('put')) {
                return [
                    'name' => 'required',
                    'address' => 'required|max:255',
                    'job' => 'required',
                    'birthdate' => 'required|date',
                    'gender' => 'required|in:Male,Female',
                    'avatar' => 'nullable|mimes:png,jpg',
                ];
            }

            $rules['address'] = 'required|max:255';
            $rules['job'] = 'required';
            $rules['birthdate'] = 'required|date';
            $rules['gender'] = 'required|in:Male,Female';
        }

        return $rules;
    }
}
