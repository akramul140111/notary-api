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
                'id'                => $application->id,
                'name'              => $application->name,
                'mobile'            => $application->mobile,
                'gender'            => $application->gender,
                'scan_copy'         => $application->scan_copy ? ScanCopyResource::collection($application->scan_copy) : [],
                'email'             => $application->email,
                'office_id'         => $application->office_id,
                'service_main_id'   => $application->service_main_id,
                'service_id'        => $application->service_id,
                'service_name'      => $application->service_name,
                'application_date'  => $this->created_at->format("Y-m-d"),
            ];
        })->all() ;
    }
}
