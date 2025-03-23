<?php

namespace Database\Seeders\admin;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include_once(database_path('data/SettingData.php'));
        if (!$data) {
            throw new \Exception('Error reading SettingData.php file ' . database_path('data/SettingData.php'));
        }
        $setting = Setting::query()->first();
        if (!$setting) {
            Setting::query()->create($data);
        } else {
            $setting->update($data);
        }
    }
}
