<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat perusahaan
        $company1 = Company::create([
            'company_name' => 'Tech Solutions',
            'description' => 'A leading tech solutions provider.',
            
            'address' => '123 Tech Street',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'working_hour_start' => '09:00',
            'working_hour_end' => '17:00',
            'status' => 'Active',
        ]);

        $company2 = Company::create([
            'company_name' => 'Creative Agency',
            'description' => 'Innovative agency for creative solutions.',
            'photo' => null,
            'address' => '456 Creative Lane',
            'latitude' => -6.250000,
            'longitude' => 106.810000,
            'working_hour_start' => '10:00',
            'working_hour_end' => '18:00',
            'status' => 'Active',
        ]);

        $company3 = Company::create([
            'company_name' => 'Finance Corp',
            'description' => 'Financial services and consulting.',
            'photo' => null,
            'address' => '789 Finance Blvd',
            'latitude' => -6.300000,
            'longitude' => 106.700000,
            'working_hour_start' => '08:30',
            'working_hour_end' => '17:30',
            'status' => 'Inactive',
        ]);

        // Membuat 20 pengguna untuk setiap perusahaan
        foreach ([$company1, $company2, $company3] as $company) {
            for ($i = 1; $i <= 20; $i++) {
                User::create([
                    'name' => 'User ' . $i . ' ' . $company->company_name,
                    'email' => strtolower('user' . $i . '@' . strtolower(str_replace(' ', '', $company->company_name)) . '.com'),
                    'password' => bcrypt('password123'),
                    'company_id' => $company->id,
                    'role' => 'user',
                    'photo' => $i, // Ganti dengan ID foto yang sesuai jika ada
                    'status' => 'Active',
                    'departement' => 'Department ' . $i,
                ]);
            }
        }
    }
}
