<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PayableAccount\Models\PayableAccount;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = ['Aluguel','Celes','Internet casa','Cartão Conan','Cartão Emilly', 'Transporte Conan', 'Transporte Emilly', 'Internet cell Emilly'
        ,'Internet cell Conan', 'Unimed Conan','Comida / Flash', 'Uniodonto Conan'];
        $insertData = [];
        foreach ($accounts as $account) {
            $insertData[] = [
                'name' => $account,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        PayableAccount::query()->insert($insertData);
    }
}
