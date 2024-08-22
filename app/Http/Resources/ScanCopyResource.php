<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ScanCopyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'application_id'    => $this->application_id,
            'scan_copy'         => $this->scan_copy? config("services.app.base_url").Storage::url($this->scan_copy) : "",
            'title'             => $this->title,
        ];
    }
}
