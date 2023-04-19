<?php

namespace App\Http\Requests\Setting\Banner;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
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
