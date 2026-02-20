<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = [
            [
                'name' => 'Tech',
                'icon' => 'Cpu',
            ],
            [
                'name' => 'Wellness',
                'icon' => 'HeartPulse',
            ],
            [
                'name' => 'Marketing',
                'icon' => 'ChartCandlestick',
            ],
            [
                'name' => 'Gaming',
                'icon' => 'Joystick',
            ],
            [
                'name' => 'Dating',
                'icon' => 'MessageCircleHeart',
            ],
            [
                'name' => 'Food & Drink',
                'icon' => 'UtensilsCrossed',
            ],
        ];

        $data = collect($categories)->map(function ($category) {
            return [
                'name'       => $category['name'],
                'slug'       => Str::slug($category['name']),
                'icon'       => $category['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('categories')->insert($data);
    }
}
