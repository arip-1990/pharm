<?php

namespace App\Http\Requests\Panel\Product;

use Illuminate\Foundation\Http\FormRequest;

class DescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['description' => 'string|nullable'];
    }
}
