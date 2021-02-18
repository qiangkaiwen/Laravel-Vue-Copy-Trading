<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            [
                'id' => 1,
                'name' => "admin",
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'roles' => [UserRole::ROLE_ADMIN],
                'date_of_birth' => '1993-05-14',
                'active' => 1,
            ],
            [
                'id' => 2,
                'name' => "user",
                'email' => 'user@gmail.com',
                'password' => Hash::make('password'),
                'roles' => [UserRole::ROLE_USER],
                'date_of_birth' => '1993-05-14',
                'active' => 1,
            ],
            [
                'id' => 3,
                'name' => "manager",
                'email' => 'manager@gmail.com',
                'password' => Hash::make('password'),
                'roles' => [UserRole::ROLE_MANAGER],
                'date_of_birth' => '1993-05-14',
                'active' => 1,
            ],
        );

        for ($i = 0; $i < count($data); $i++)
            User::create($data[$i]);
    }
}
