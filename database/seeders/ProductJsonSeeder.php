<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductJsonSeeder extends Seeder
{
    /**
     * Seed sample data directly to JSON file (no database)
     *
     * @return void
     */
    public function run()
    {
        $dataFile = storage_path('app/products.json');
        
        $sampleProducts = [
            [
                'id' => uniqid('prod_', true), 
                'product_name' => 'Laptop Computer',
                'quantity' => 10,
                'price' => 999.99,
                'submitted_at' => Carbon::now()->subDays(2)->toIso8601String(), 
                'total_value' => 10 * 999.99
            ],
            [
                'id' => uniqid('prod_', true),
                'product_name' => 'Wireless Mouse',
                'quantity' => 25,
                'price' => 29.99,
                'submitted_at' => Carbon::now()->subDays(1)->toIso8601String(),
                'total_value' => 25 * 29.99
            ],
            [
                'id' => uniqid('prod_', true),
                'product_name' => 'USB Cable',
                'quantity' => 50,
                'price' => 9.99,
                'submitted_at' => Carbon::now()->toIso8601String(),
                'total_value' => 50 * 9.99
            ]
        ];

        // Ensure storage directory exists
        $directory = dirname($dataFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save as properly formatted JSON
        $jsonData = json_encode($sampleProducts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($dataFile, $jsonData);
        
        $this->command->info('Sample products saved to JSON file: ' . $dataFile);
        $this->command->info('You can now view them at: http://localhost:8000');
    }
}