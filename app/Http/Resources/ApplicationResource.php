<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array|\JsonSerializable|Arrayable
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'mobile'    => $this->mobile,
            'gender'    => $this->gender,
            'scan_copy' => Storage::url($this->scan_copy),
            'email'     => $this->email,
        ];
    }
}
