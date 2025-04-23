<?php

namespace App\Http\Controllers\Api\Widget;

use App\Exceptions\WidgetNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Widget\CreateWidgetRequest;
use App\Http\Requests\Widget\EditWidgetRequest;
use App\Http\Resources\Widget\WidgetResource;
use App\Models\Widget;
use App\Services\Admin\Widget\WidgetService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends Controller
{
    protected WidgetService $widgetService;

    public function __construct(WidgetService $widgetService, Request $request)
    {
        $this->widgetService = $widgetService;
    }

    public function index(Request $request) {
        $this->widgetService->setUser($request->user()->user);
        return WidgetResource::collection(
            $this->widgetService->getWidgetRepository()->findBySite(
                $request->user()->site
            )
        );
    }
    public function view(Widget $widget, Request $request) {
        return new WidgetResource($widget);
    }

    public function create(CreateWidgetRequest $request) {
        $this->widgetService->setUser($request->user()->user);
        $create = $this->widgetService->createWidget(
            $request->user()->site,
            $request->validated()
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating app widget',
                'errors' => $this->widgetService->getResultsService()->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Widget created',
        ]);
    }

    public function update(Widget $widget, EditWidgetRequest $request) {
        $this->widgetService->setUser($request->user()->user);
        $this->widgetService->setSite($request->user()->site);
        $update = $this->widgetService->updateWidget(
            $widget,
            $request->validated()
        );
        if (!$update) {
            return response()->json([
                'message' => 'Error updating app widget',
                'errors' => $this->widgetService->getResultsService()->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Widget updated',
        ]);
    }
    public function destroy(Widget $widget) {
        $delete = $this->widgetService->deleteWidget($widget);
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting app widget',
                'errors' => $this->widgetService->getResultsService()->getErrors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Widget deleted',
        ]);
    }
}
