<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name'          => 'required',
            'mobile'        => 'required',
            'gender'        => 'required',
            'service_id'    => 'required',
            // 'scan_copies.*.title' => 'nullable|string',
            // 'scan_copies.*.appImg' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ];
    }
}
