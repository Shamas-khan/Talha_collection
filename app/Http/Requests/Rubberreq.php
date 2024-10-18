<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Rubberreq extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => 'required',
            'raw_material_id' => 'required',
            'unit_id.*' => 'required',
            'qty.*' => 'required',
            'kilogram.*' => 'nullable|numeric',
            'sheet.*' => 'required',
            'unit_price.*' => 'required',
            'total.*' => 'required',
            'transport_charges' => 'required',
            'gandtotal' => 'required',
           
            'unit_name' => 'required',
            
        ];
    }
}
