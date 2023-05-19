<?php

namespace App\Http\Requests\Panel\Product;

use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|integer',
            'barcodes' => 'array|nullable',
            'category' => 'integer|nullable',
            'marked' => 'boolean|nullable',
            'recipe' => 'boolean|nullable',
            'showMain' => 'boolean|nullable',
        ];
    }
}
