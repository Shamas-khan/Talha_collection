<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueRequest extends FormRequest
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
            'vendor' => 'required',
            'product' => 'required',
            'total_qty' => 'required',
            'unit_price' => 'required',
            'total' => 'required',
            'customer_id' => 'required',
            'total_cost' => 'required', 
            'unit_cost' => 'required', 
            
        ];
    }
}
