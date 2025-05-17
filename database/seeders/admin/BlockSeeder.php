<?php

namespace Database\Seeders\admin;

use App\Enums\Block\BlockType;
use App\Models\Block;
use App\Services\Block\BlockService;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(BlockService $blockService): void
    {
        $blockService->defaultBlockTypes();
    }
}
