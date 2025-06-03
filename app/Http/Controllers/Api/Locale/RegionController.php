<?php

namespace App\Http\Controllers\Api\Locale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Region\StoreRegionRequest;
use App\Http\Requests\Region\UpdateRegionRequest;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use App\Repositories\RegionRepository;
use App\Services\Locale\RegionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends Controller
{

    public function __construct(
        private RegionService $regionService,
        private RegionRepository $regionRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->regionRepository->setPagination(true);
        $this->regionRepository->setOrderByColumn(
            $request->get('sort', 'name')
        );
        $this->regionRepository->setOrderByDir(
            $request->get('order', 'asc')
        );
        $this->regionRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->regionRepository->setPage(
            $request->get('page', 1)
        );
        
        $search = $request->get('query', null);
        if ($search) {
            $this->regionRepository->addWhere(
                'name',
                "%$search%",
                'like',
            );
        }
        return RegionResource::collection(
            $this->regionRepository->findMany()
        );
    }

    public function show(Region $region, Request $request) {
        $this->regionService->setUser($request->user()->user);
        $this->regionService->setSite($request->user()->site);
        return new RegionResource($region);
    }

    public function store(StoreRegionRequest $request) {
        $this->regionService->setUser($request->user()->user);
        $this->regionService->setSite($request->user()->site);

        if (!$this->regionService->createRegion($request->validated())) {
            return response()->json([
                'message' => 'Error creating region',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Region created',
        ], Response::HTTP_CREATED);
    }

    public function update(Region $region, UpdateRegionRequest $request) {
        $this->regionService->setUser($request->user()->user);
        $this->regionService->setSite($request->user()->site);

        if (!$this->regionService->updateRegion($region, $request->validated())) {
            return response()->json([
                'message' => 'Error updating region',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Region updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Region $region, Request $request) {
        $this->regionService->setUser($request->user()->user);
        $this->regionService->setSite($request->user()->site);

        if (!$this->regionService->deleteRegion($region)) {
            return response()->json([
                'message' => 'Error deleting region',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Region deleted',
        ], Response::HTTP_OK);
    }
}
