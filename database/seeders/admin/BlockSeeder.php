<?php

namespace Database\Seeders\admin;

use App\Models\Block;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include_once(database_path('data/BlockData.php'));
        if (!$data) {
            throw new \Exception('Error reading BlockData.php file ' . database_path('data/BlockData.php'));
        }
        foreach ($data as $item) {
            $create = Block::query()->updateOrCreate(
                ['type' => $item['type']],
                $item
            );
        }
    }
}
