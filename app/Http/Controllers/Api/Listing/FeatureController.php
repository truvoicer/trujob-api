<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feature\StoreFeatureRequest;
use App\Http\Requests\Feature\UpdateFeatureRequest;
use App\Http\Resources\Listing\FeatureResource;
use App\Models\Feature;
use App\Repositories\FeatureRepository;
use App\Services\Feature\FeatureService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeatureController extends Controller
{

    public function __construct(
        private FeatureService $featureService,
        private FeatureRepository $featureRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $this->featureRepository->setPagination(true);
        $this->featureRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->featureRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->featureRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->featureRepository->setPage(
            $request->get('page', 1)
        );
        
        return FeatureResource::collection(
            $this->featureRepository->findMany()
        );
    }

    public function create(StoreFeatureRequest $request) {
        $this->featureService->setUser($request->user()->user);
        $this->featureService->setSite($request->user()->site);

        if (!$this->featureService->createFeature($request->validated())) {
            return response()->json([
                'message' => 'Error creating feature',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Feature created',
        ], Response::HTTP_CREATED);
    }

    public function update(Feature $feature, UpdateFeatureRequest $request) {
        $this->featureService->setUser($request->user()->user);
        $this->featureService->setSite($request->user()->site);

        if (!$this->featureService->updateFeature($feature, $request->validated())) {
            return response()->json([
                'message' => 'Error updating listing feature',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing feature updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Feature $feature, Request $request) {
        $this->featureService->setUser($request->user()->user);
        $this->featureService->setSite($request->user()->site);
        
        if (!$this->featureService->deleteFeature($feature)) {
            return response()->json([
                'message' => 'Error deleting listing feature',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing feature deleted',
        ], Response::HTTP_OK);
    }
}
