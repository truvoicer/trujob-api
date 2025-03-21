<?php

namespace Database\Seeders\admin;

use App\Models\Menu;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::all()->each(function ($page) {
            Menu::factory()->create([
                'page_id' => $page->id,
                'name' => 'Settings',
                'icon' => 'fas fa-cogs',
                'url' => '/admin/settings',
                'order' => 1,
            ]);
        });
    }
}
