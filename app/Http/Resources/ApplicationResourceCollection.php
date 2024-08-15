<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class ApplicationResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request) : array|\JsonSerializable|Arrayable
    {
        return $this->collection->map(function($application) {
            return [
                'id'        => $application->id,
                'name'      => $application->name,
                'mobile'    => $application->mobile,
                'gender'    => $application->gender,
                'scan_copy' => Storage::url($application->scan_copy),
                'email'     => $application->email,
            ];
        })->all() ;
    }
}
