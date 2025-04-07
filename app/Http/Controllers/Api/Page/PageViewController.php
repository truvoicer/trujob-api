<?php

namespace App\Http\Controllers\Api\Page;

use App\Enums\ViewType;
use App\Http\Controllers\Controller;
use App\Repositories\PageRepository;
use App\Services\Page\PageService;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PageViewController extends Controller
{
    public function __construct(
        private PageService $pageService,
        private PageRepository $pageRepository
    )
    {
    }

    public function index()
    {
        return response()->json([
            'message' => 'Page view controller index method',
            'data' => ViewType::cases(),
        ]);
    }

}
