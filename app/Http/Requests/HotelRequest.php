<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'country' => 'required|string',
            'location' => 'required|string',
            'checkIn' => 'required|date|date_format:d-m-Y|before:checkOut',
            'checkOut' => 'required|date|date_format:d-m-Y|after:checkIn',
        ];
    }
}
