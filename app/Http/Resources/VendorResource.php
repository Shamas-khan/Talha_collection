<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'vendor_id ' => $this->vendor_id,
            'name' => $this->name,
            'contact' => $this->contact,
            'cnic' => $this->cnic,
            'address' => $this->address,
        ];
    }
}
