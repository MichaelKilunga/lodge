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
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'required|string|max:30',
            'avatar' => 'nullable|mimes:png,jpg',
        ];

        if ($this->routeIs('transaction.reservation.storeCustomer')) {
            $rules['email'] = 'nullable|email';
            $rules['phone'] = 'required|string|max:30';
            $rules['address'] = 'nullable|max:255';
            $rules['job'] = 'nullable';
            $rules['birthdate'] = 'nullable|date';
            $rules['gender'] = 'nullable|in:Male,Female';
        } else {
            if ($this->isMethod('put')) {
                $customer = $this->route('customer');
                $userId = $customer && $customer->user ? $customer->user->id : null;

                return [
                    'name' => 'required',
                    'email' => 'nullable|email|unique:users,email,' . $userId,
                    'phone' => 'required|string|max:30',
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
