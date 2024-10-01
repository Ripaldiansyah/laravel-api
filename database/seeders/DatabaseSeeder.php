<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'John Doe',
            'email' => 'hrd@mail.com',
            'password' => bcrypt('password123'),
            'company_id' => 1,
            'role' => 'hrd',
            'photo' => 1,
            'status' => 'Active',
            'departement' => 'HR',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'user@mail.com',
            'password' => bcrypt('password123'),
            'company_id' => 1,
            'role' => 'user',
            'photo' => 2,
            'status' => 'Active',
            'departement' => 'Finance',
        ]);

        User::create([
            'name' => 'Alice Johnson',
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
            'company_id' => 2,
            'role' => 'user',
            'photo' => 3,
            'status' => 'Inactive',
            'departement' => 'Marketing',
        ]);

        Company::create([
            'company_name' => 'Tech Solutions',
            'description' => 'A leading tech solutions provider.',
            'photo' => null,
            'address' => '123 Tech Street',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'working_hour_start' => '09:00',
            'working_hour_end' => '17:00',
            'status' => 'Active',
        ]);

        Company::create([
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

        Company::create([
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
    }
}
