<?php

namespace App\Http\Requests\Setting\Banner;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'type' => 'string|nullable',
            'description' => 'string|nullable',
            'files' => 'required|array',
            'files.main' => 'required|mimes:webp,jpg,jpeg',
            'files.mobile' => 'nullable|mimes:webp,jpg,jpeg',
        ];
    }
}
