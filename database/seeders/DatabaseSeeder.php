<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
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
            'photo' => null,
            'address' => '123 Tech Street',
            'store_hour_start' => '09:00',
            'store_hour_end' => '17:00',
            'status' => 'Active',
        ]);

        $company2 = Company::create([
            'company_name' => 'Creative Agency',
            'description' => 'Innovative agency for creative solutions.',
            'photo' => null,
            'address' => '456 Creative Lane',
            'store_hour_start' => '10:00',
            'store_hour_end' => '18:00',
            'status' => 'Active',
        ]);


        $company3 = Company::create([
            'company_name' => 'Finance Corp',
            'description' => 'Financial services and consulting.',
            'photo' => null,
            'address' => '789 Finance Blvd',

            'store_hour_start' => '08:30',
            'store_hour_end' => '17:30',
            'status' => 'Inactive',
        ]);

        $categories = [
            'Electronics',
            'Furniture',
            'Stationery',
            'Clothing',
        ];
        $categories_icon = [
            'https://img.icons8.com/?size=100&id=11144&format=png&color=000000',
            'https://img.icons8.com/?size=100&id=y2GWL3nrlTBH&format=png&color=000000',
            'https://img.icons8.com/?size=100&id=9958&format=png&color=000000',
            'https://img.icons8.com/?size=100&id=25497&format=png&color=000000',
        ];
        $index = 0;
        foreach ($categories as $category) {

            Category::create([
                'category_name' => $category,
                'icon' => $categories_icon[$index],
                'company_id' => $company1->id,
            ]);
            $index++;
        }


        for ($i = 0; $i < 20; $i++) {
            $products = [
                ['product_name' => "Laptop . $i", 'price' => 1500.00, 'stock' => 10, 'sku' => "LAP123 . $i", 'category_id' => 1],
                ['product_name' => "Desk . $i", 'price' => 200.00, 'stock' => 5, 'sku' => "DESK456 . $i", 'category_id' => 2],
                ['product_name' => "Pen . $i", 'price' => 1.00, 'stock' => 100, 'sku' => "PEN789 . $i", 'category_id' => 3],
            ];

            foreach ($products as $product) {
                Product::create(array_merge($product, ['company_id' => $company1->id]));
            }
        }


        // Membuat supplier
        $suppliers = [
            ['supplier_name' => 'Supplier A', 'supplier_address' => '123 Supplier St'],
            ['supplier_name' => 'Supplier B', 'supplier_address' => '456 Supplier Rd'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create(array_merge($supplier, ['company_id' => $company1->id]));
        }

        // Membuat transaksi penjualan


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
