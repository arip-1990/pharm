<?php

namespace App\Http\Requests\V1\PhotoKids;

use Illuminate\Foundation\Http\FormRequest;

class PhotoKidsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Разрешить выполнение запроса, можно добавить дополнительную логику
        return true;
    }

    public function rules(): array
    {
        return [
            'photo_name' => 'required|string|max:255',
            'birthdate' => 'nullable|date',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'user_id' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048'
        ];
    }

    public function attributes(): array
    {
        return [
            'photo_name' => 'Название фото',
            'birthdate' => 'Дата рождения',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'user_id' => 'ID пользователя',
            'file' => 'Файл фото'
        ];
    }

    public function messages(): array
    {
        return [
            'photo_name.required' => 'Название фото обязательно для заполнения.',
            'file.required' => 'Загрузка фото обязательна.',
            'file.mimes' => 'Фото должно быть в формате jpg, jpeg или png.',
            'file.max' => 'Размер фото не должен превышать 2MB.'
        ];
    }
}
