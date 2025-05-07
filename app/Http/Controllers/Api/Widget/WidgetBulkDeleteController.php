<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Widget\WidgetBulkDeleteRequest;
use App\Repositories\WidgetRepository;
use App\Services\Admin\Widget\WidgetService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class WidgetBulkDeleteController extends Controller
{
    public function __construct(
        private WidgetService $widgetService,
        private WidgetRepository $widgetRepository
    ) {}

    public function __invoke(WidgetBulkDeleteRequest $request)
    {
        $this->widgetService->setUser($request->user()->user);
        $this->widgetService->setSite($request->user()->site);
        if (!$this->widgetService->deleteBulkWidgets($request->validated('ids'))) {
            return response()->json([
                'message' => 'Widgets could not be deleted.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Widgets deleted successfully.'
        ], Response::HTTP_OK);
    }
}
