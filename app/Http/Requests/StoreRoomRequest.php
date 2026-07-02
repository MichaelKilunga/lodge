<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'type_id' => 'required',
            'room_status_id' => 'required',
            'capacity' => 'required|numeric',
            'price' => 'required|numeric',
            'view' => 'required|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];

        if (! empty($this->room->id)) {
            $rules['number'] = 'required|unique:rooms,number,'.$this->room->id;
        } else {
            $rules['number'] = 'required|unique:rooms,number';
        }

        return $rules;
    }
}
