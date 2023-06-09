<?php

namespace App\Http\Requests\Setting\Banner;

use App\Setting\Entity\BannerType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
            'type' => ['nullable', new Enum(BannerType::class)],
            'description' => 'string|nullable',
            'path' => 'string|nullable',
            'link' => 'string|nullable',
            'files' => 'required|array',
            'files.main' => 'required|mimes:webp,jpg,jpeg',
            'files.mobile' => 'nullable|mimes:webp,jpg,jpeg',
        ];
    }
}
