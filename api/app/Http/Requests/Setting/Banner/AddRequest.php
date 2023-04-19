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
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'file' => 'required|mimes:jpg,jpeg,png'
        ];
    }
}
