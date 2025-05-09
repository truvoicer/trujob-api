<?php

namespace App\Http\Controllers\Api\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Resources\Listing\BrandResource;
use App\Models\Brand;
use App\Repositories\BrandRepository;
use App\Services\Brand\BrandService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{

    public function __construct(
        private BrandService $brandService,
        private BrandRepository $brandRepository,
    )
    {
    }

    public function index(Request $request) {
        $this->brandRepository->setPagination(true);
        $this->brandRepository->setSortField(
            $request->get('sort', 'label')
        );
        $this->brandRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->brandRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->brandRepository->setPage(
            $request->get('page', 1)
        );
        
        return BrandResource::collection(
            $this->brandRepository->findMany()
        );
    }

    public function create(StoreBrandRequest $request) {
        $this->brandService->setUser($request->user()->user);
        $this->brandService->setSite($request->user()->site);

        if (!$this->brandService->createBrand($request->all())) {
            return response()->json([
                'message' => 'Error creating brand',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Brand created',
        ], Response::HTTP_CREATED);
    }

    public function update(Brand $brand, UpdateBrandRequest $request) {
        $this->brandService->setUser($request->user()->user);
        $this->brandService->setSite($request->user()->site);

        if (!$this->brandService->updateBrand($brand, $request->all())) {
            return response()->json([
                'message' => 'Error updating brand',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Brand updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Brand $brand, Request $request) {
        $this->brandService->setUser($request->user()->user);
        $this->brandService->setSite($request->user()->site);

        if (!$this->brandService->deleteBrand($brand)) {
            return response()->json([
                'message' => 'Error deleting brand',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Brand deleted',
        ], Response::HTTP_OK);
    }

}
