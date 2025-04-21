<?php

namespace App\Http\Controllers\Api\Link;

use App\Enums\LinkTarget;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class LinkTargetController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => LinkTarget::cases(),
        ]);
    }

}
