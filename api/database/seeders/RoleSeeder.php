<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'user' => [
                'name' => 'user',
                'description' => 'Пользователь',
            ],
            'manager' => [
                'name' => 'manager',
                'description' => 'Менеджер',
            ],
            'admin' => [
                'name' => 'admin',
                'description' => 'Администратор',
            ]
        ];

        foreach ($data as $item) Role::firstOrCreate($item);
    }
}
