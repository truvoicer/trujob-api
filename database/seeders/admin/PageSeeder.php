<?php

namespace Database\Seeders\admin;

use App\Models\Block;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = include_once(database_path('data/PageData.php'));
        if (!$data) {
            throw new \Exception('Error reading PageData.php file ' . database_path('data/PageData.php'));
        }
        foreach ($data as $item) {
            $blocks = [];
            if (!empty($item['blocks']) && is_array($item['blocks'])) {
                $blocks = $item['blocks'];
            }
            if (!empty($item['blocks'])) {
                unset($item['blocks']);
            }
            $create = Page::query()->updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
            foreach ($blocks as $block) {
                $findBlock = Block::where('type', $block['type'])->first();
                if (!$findBlock) {
                    throw new \Exception('Block not found ' . $block['type']);
                }
                $create->blocks()->attach($findBlock->id, [
                    'properties' => json_encode($block['properties']),
                    'order' => $block['order']
                ]);
            }
        }
    }
}
