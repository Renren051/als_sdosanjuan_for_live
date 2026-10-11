<?php

namespace Tests\Feature;

use App\Models\HeroBanner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class HeroBannerTest extends TestCase
{

    public function test_homepage_renders_hero_banner_carousel(): void
    {
        $banner = HeroBanner::updateOrCreate(
            ['title' => 'Alternative Learning System San Juan'],
            [
                'subtitle' => 'The Best Parallel Learning System',
                'image_path' => 'images/banners/hero-slide-1.jpg',
                'button_text' => 'Take A Tour',
                'button_url' => '/register',
                'order_seq' => 1,
                'is_active' => true,
            ]
        );

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Alternative Learning System San Juan');
        $response->assertSee('The Best Parallel Learning System');
        $response->assertSee('Take A Tour');
        $response->assertDontSee('START YOUR DEPED JOURNEY IN SAN JUAN TODAY');
    }

    public function test_admin_can_view_banners_index(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::create([
            'username' => 'testadmin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.banners.index'));

        $response->assertStatus(200);
        $response->assertSee('Homepage Hero Banners');
        $response->assertSee('Upload New Banner Photo');
    }

    public function test_admin_can_upload_new_hero_banner(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::create([
            'username' => 'testadmin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $fakeImage = UploadedFile::fake()->image('test_banner.jpg', 1200, 600);

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'New Banner Test',
            'subtitle' => 'Subtitle Test',
            'button_text' => 'Explore Now',
            'button_url' => '/programs',
            'order_seq' => 5,
            'is_active' => 1,
            'image' => $fakeImage,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('hero_banners', [
            'title' => 'New Banner Test',
            'subtitle' => 'Subtitle Test',
            'button_text' => 'Explore Now',
            'button_url' => '/programs',
        ]);

        // Clean up uploaded fake file and test banner
        $banner = HeroBanner::where('title', 'New Banner Test')->first();
        if ($banner) {
            if (File::exists(public_path($banner->image_path))) {
                File::delete(public_path($banner->image_path));
            }
            $banner->delete();
        }
    }

    public function test_admin_can_toggle_banner_active_status(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::create([
            'username' => 'testadmin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $banner = HeroBanner::create([
            'title' => 'Toggle Status Banner',
            'subtitle' => 'Subtitle',
            'image_path' => 'images/banners/hero-slide-1.jpg',
            'button_text' => 'Tour',
            'button_url' => '/register',
            'order_seq' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.banners.toggle', $banner->id));

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('hero_banners', [
            'id' => $banner->id,
            'is_active' => false,
        ]);

        $banner->delete();
    }

    public function test_admin_can_upload_new_banner_with_cropped_image(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::create([
            'username' => 'testadmin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        // Minimal valid 1x1 base64 JPEG
        $croppedBase64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->actingAs($admin)->post(route('admin.banners.store'), [
            'title' => 'Cropped Banner Test',
            'subtitle' => 'Cropped Subtitle',
            'button_text' => 'Join Now',
            'button_url' => '/register',
            'order_seq' => 8,
            'is_active' => 1,
            'cropped_image' => $croppedBase64,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('hero_banners', [
            'title' => 'Cropped Banner Test',
            'button_text' => 'Join Now',
        ]);

        $banner = HeroBanner::where('title', 'Cropped Banner Test')->first();
        $this->assertNotNull($banner);
        $this->assertFileExists(public_path($banner->image_path));

        // Clean up
        if (File::exists(public_path($banner->image_path))) {
            File::delete(public_path($banner->image_path));
        }
        $banner->delete();
    }

    public function test_admin_can_reposition_existing_banner(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::create([
            'username' => 'testadmin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $banner = HeroBanner::create([
            'title' => 'Banner To Reposition',
            'subtitle' => 'Subtitle',
            'image_path' => 'images/banners/hero-slide-1.jpg',
            'button_text' => 'Tour',
            'button_url' => '/register',
            'order_seq' => 1,
            'is_active' => true,
        ]);

        $croppedBase64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $response = $this->actingAs($admin)->patch(route('admin.banners.reposition', $banner->id), [
            'cropped_image' => $croppedBase64,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $banner->refresh();
        $this->assertNotEquals('images/banners/hero-slide-1.jpg', $banner->image_path);
        $this->assertFileExists(public_path($banner->image_path));

        // Clean up
        if (File::exists(public_path($banner->image_path))) {
            File::delete(public_path($banner->image_path));
        }
        $banner->delete();
    }

    public function test_admin_can_reposition_banner_offset_without_cropping(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::create([
            'username' => 'testadmin_' . uniqid(),
            'email' => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
            'status' => 'active',
        ]);

        $banner = HeroBanner::create([
            'title' => 'Banner To Reposition Without Crop',
            'subtitle' => 'Subtitle',
            'image_path' => 'images/banners/hero-slide-1.jpg',
            'position_x' => 50,
            'position_y' => 50,
            'button_text' => 'Tour',
            'button_url' => '/register',
            'order_seq' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.banners.reposition', $banner->id), [
            'position_x' => 50,
            'position_y' => 25,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $banner->refresh();
        $this->assertEquals(50, $banner->position_x);
        $this->assertEquals(25, $banner->position_y);
        // The original image file is intact and unchanged (no destructive crop)
        $this->assertEquals('images/banners/hero-slide-1.jpg', $banner->image_path);

        $banner->delete();
    }
}
