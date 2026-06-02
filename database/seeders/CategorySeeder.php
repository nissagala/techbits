<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Keyboards',
            'Mice',
            'RAM',
            'Storage',
            'Monitors',
            'Headsets & Audio',
            'Webcams',
            'Cables & Adapters',
            'Laptop Accessories',
            'Power & Charging',
        ];

        foreach ($categories as $name) {
            DB::table('categories')->insertOrIgnore([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
