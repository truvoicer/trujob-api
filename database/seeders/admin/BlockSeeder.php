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
            $type = $item['type'];
            unset($item['type']);
            $atts = $item;
            if (!empty($item['properties'])) {
                $atts['properties'] = json_encode($item['properties']);
            }
            $create = Block::query()->updateOrCreate(
                ['type' => $type],
                $atts
            );
        }
    }
}
