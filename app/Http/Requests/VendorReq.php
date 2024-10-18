<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorReq extends FormRequest
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
            'contact' => 'required',
            'address' => 'required',
            'cnic' => 'required',
            'op_balance' => 'required',
            'transaction_type' => 'required',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'op_balance' => str_replace(',', '', $this->op_balance),
        ]);
    }
}
