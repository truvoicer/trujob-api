<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Region\BulkDestroyRegionRequest;
use App\Http\Requests\Region\BulkStoreRegionRequest;
use App\Services\Region\RegionService;
use Symfony\Component\HttpFoundation\Response;

class BulkRegionController extends Controller
{

    public function __construct(
        private RegionService $regionService
    )
    {
    }

    public function store(BulkStoreRegionRequest $request) {
        $this->regionService->setUser($request->user()->user);
        $this->regionService->setSite($request->user()->site);
        $create = $this->regionService->createRegionBatch(
            $request->validated('regions')
        );
        if (!$create) {
            return response()->json([
                'message' => 'Error creating region batch',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Region batch created',
        ], Response::HTTP_OK);
    }

    public function destroy(BulkDestroyRegionRequest $request) {
        $this->regionService->setUser($request->user()->user);
        $this->regionService->setSite($request->user()->site);
        $delete = $this->regionService->deleteRegionBatch(
            $request->validated('ids')
        );
        if (!$delete) {
            return response()->json([
                'message' => 'Error deleting region batch',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Region batch deleted',
        ], Response::HTTP_OK);
    }

}
