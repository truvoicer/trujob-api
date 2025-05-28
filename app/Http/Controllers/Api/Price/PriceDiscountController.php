<?php

namespace App\Http\Controllers\Api\Price;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\Price\Discount\StorePriceDiscountRequest;
use App\Http\Requests\Listing\Price\Discount\UpdatePriceDiscountRequest;
use App\Http\Resources\Discount\DiscountResource;
use App\Models\Discount;
use App\Models\Listing;
use App\Models\Price;
use App\Repositories\DiscountRepository;
use App\Services\Discount\DiscountService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PriceDiscountController extends Controller
{

    public function __construct(
        private DiscountService $discountService,
        private DiscountRepository $discountRepository,
    ) {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Listing $listing, Price $price, Request $request)
    {
        $this->discountRepository->setQuery(
            $price->discounts()
        );
        $this->discountRepository->setPagination(true);
        $this->discountRepository->setSortField(
            $request->get('sort', 'name')
        );
        $this->discountRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->discountRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->discountRepository->setPage(
            $request->get('page', 1)
        );

        return DiscountResource::collection(
            $this->discountRepository->findMany()
        );
    }

    public function show(Listing $listing, Price $price, Discount $discount, Request $request)
    {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);

        $check = $price->discounts()->where('discounts.id', $discount->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Discount does not exist for this price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return new DiscountResource($discount);
    }

    public function store(Listing $listing, Price $price, StorePriceDiscountRequest $request)
    {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);
        $data = $request->validated();
        $data['prices'] = [];
        $data['prices'][] = $price->id;
        if (!$this->discountService->createDiscount($data)) {
            return response()->json([
                'message' => 'Error creating discount',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Discount created',
        ], Response::HTTP_CREATED);
    }

    public function update(Listing $listing, Price $price, Discount $discount, UpdatePriceDiscountRequest $request)
    {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);

        $check = $price->discounts()->where('id', $discount->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Discount does not exist for this price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        if (!$this->discountService->updateDiscount($discount, $request->validated())) {
            return response()->json([
                'message' => 'Error updating discount',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Discount updated',
        ], Response::HTTP_OK);
    }
    
    public function destroy(Listing $listing, Price $price, Discount $discount, Request $request)
    {
        $this->discountService->setUser($request->user()->user);
        $this->discountService->setSite($request->user()->site);

        $check = $price->discounts()->where('discounts.id', $discount->id)->exists();
        if (!$check) {
            return response()->json([
                'message' => 'Discount does not exist for this price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        if (!$this->discountService->deleteDiscount($discount)) {
            return response()->json([
                'message' => 'Error deleting discount',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Discount deleted',
        ], Response::HTTP_OK);
    }
}
