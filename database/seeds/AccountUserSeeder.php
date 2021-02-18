<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\UserAccounts;

class AccountUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < count(AccountSeeder::ACCOUNT_NUMBERS); $i++)
            UserAccounts::create([
                'id' => $i + 1,
                'user_id' => 1,
                'account_id' => $i + 1,
            ]);
    }
}
