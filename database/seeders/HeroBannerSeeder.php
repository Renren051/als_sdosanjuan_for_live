<?php

namespace Database\Seeders;

use App\Models\HeroBanner;
use Illuminate\Database\Seeder;

class HeroBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'image_path' => 'images/banners/hero-slide-1.jpg',
                'subtitle' => 'The Best Parallel Learning System Of San Juan City',
                'title' => 'Alternative Learning System',
                'button_text' => 'Take A Tour',
                'button_url' => '/register',
                'order_seq' => 1,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/banners/hero-slide-2.jpg',
                'subtitle' => 'Quality Community-Based Modular Education',
                'title' => 'Edukasyon sa Pagbabago',
                'button_text' => 'Locate Centers',
                'button_url' => '/learning-centers',
                'order_seq' => 2,
                'is_active' => true,
            ],
            [
                'image_path' => 'images/banners/hero-slide-3.jpg',
                'subtitle' => 'DepEd Elementary & Junior High Equivalency',
                'title' => 'Rebuild Your Future',
                'button_text' => 'Enroll Online',
                'button_url' => '/register',
                'order_seq' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $data) {
            HeroBanner::updateOrCreate(
                ['image_path' => $data['image_path']],
                $data
            );
        }
    }
}
