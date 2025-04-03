<?php

namespace App\Http\Controllers\Api\Block;

use App\Http\Controllers\Controller;
use App\Http\Resources\Block\BlockResource;
use App\Models\Block;
use App\Services\Page\PageService;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class BlockController extends Controller
{
    public function __construct(
        private PageService $pageService
    )
    {
    }

    public function index()
    {
        return BlockResource::collection(
            Block::all()
        );
    }

    public function view(Block $block)
    {
        return new BlockResource($block);
    }

}
