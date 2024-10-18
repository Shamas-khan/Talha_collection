<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class packingreq extends FormRequest
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
            'small_shoper_id' => 'required',
            'small_product_qty' => 'required',
            'big_shoper_id' => 'required',
           'big_product_qty' => 'required',
            'finish_product_id' => 'required',
        ];
    }
}
