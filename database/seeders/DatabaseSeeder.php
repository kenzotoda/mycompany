<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'name' => 'Empresa Demo MEI',
            'trade_name' => 'Demo MEI',
            'document' => '00.000.000/0001-00',
            'email' => 'contato@demo.local',
            'phone' => '(11) 99999-9999',
        ]);

        $this->call(RolesAndPermissionsSeeder::class);

        User::factory()->create([
            'company_id' => $company->id,
            'name' => 'Administrador',
            'email' => 'admin@demo.local',
            'access_level' => 'admin',
        ])->assignRole('admin');
    }
}
