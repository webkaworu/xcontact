<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Persistence\Eloquent\Plan;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::firstOrCreate(
            ['name' => '無料プラン'],
            [
                'description' => '基本的な機能が利用できる無料プラン',
                'form_limit' => 1,
                'monthly_limit' => 100,
                'price' => 0.00,
            ]
        );

        Plan::firstOrCreate(
            ['name' => '有料プラン'],
            [
                'description' => 'より多くの機能と高い制限が利用できる有料プラン',
                'form_limit' => 1,
                'monthly_limit' => 1000,
                'price' => 9.99,
            ]
        );
    }
}
