<?php

namespace App\Http\Controllers\Api\Pagination;

use App\Enums\Pagination\PaginationType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PaginationTypeController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => PaginationType::cases(),
        ]);
    }

}
