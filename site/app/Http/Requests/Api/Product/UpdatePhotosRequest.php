<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|max:5',
            'items.*.id' => 'required|integer|distinct',
            'items.*.sort' => 'required|integer|distinct',
        ];
    }
}
