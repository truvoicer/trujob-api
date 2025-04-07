<?php

namespace App\Http\Controllers\Api\Pagination;

use App\Enums\Pagination\PaginationScrollType;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class PaginationScrollTypeController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => PaginationScrollType::cases(),
        ]);
    }

}
