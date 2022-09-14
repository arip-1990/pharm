<?php

namespace App\Http\Requests\Panel\Product;

use Illuminate\Foundation\Http\FormRequest;

class DeletePhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*' => 'required|integer|distinct',
        ];
    }
}
