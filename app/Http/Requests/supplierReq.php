<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class supplierReq extends FormRequest
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
            'name' => 'required',
            'company' => 'required',
            'op_balance' => 'required|numeric',
            'contact' => 'required',
            'address' => 'required',
            'transaction_type' => 'required',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'op_balance' => str_replace(',', '', $this->op_balance),
        ]);
    }
}