<?php

namespace Database\Seeders\admin;

use App\Enums\Block\BlockType;
use App\Models\Block;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (BlockType::cases() as $blockType) {
            $atts = [
                'type' => $blockType->value,
            ];
            $create = Block::query()->updateOrCreate(
                ['type' => $blockType],
                $atts
            );
        }
    }
}
