<?php

namespace App\Http\Requests\Setting\Banner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSortRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.id' => 'required|integer|distinct',
            'items.*.sort' => 'required|integer|distinct',
        ];
    }
}
