<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Accounts;

class AccountSeeder extends Seeder
{
    const ACCOUNT_NUMBERS = [
        '1234560',
        '1234561',
        '1234562',
        '1234563',
        '1234564',
        '1234565',
        '1234566',
        '1234567',
        '1234568',
        '1234569',
        '1234570',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i = 0; $i < count(self::ACCOUNT_NUMBERS); $i++)
            Accounts::create([
                'id' => $i + 1,
                "account_number" => self::ACCOUNT_NUMBERS[$i],
                "broker" => Str::random(10),
            ]);
    }
}
