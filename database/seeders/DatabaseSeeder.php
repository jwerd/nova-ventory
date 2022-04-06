<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $company = \App\Models\Company::updateOrCreate(['id' => 1, 'name' => 'Wendy\'s Garage']);
        \App\Models\User::updateOrCreate([
            'name' => 'Jake',
            'company_id' => $company->id,
            'email' => 'jacob@sadsoft.com',
            'password' => bcrypt('secret')
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
