<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PageController extends Controller
{

    public function index(Request $request)
    {
        return [];
    }

    public function edit(Page $page)
    {
        return new PageResource($page);
    }

}
