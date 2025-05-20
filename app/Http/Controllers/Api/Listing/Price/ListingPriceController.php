<?php

namespace App\Http\Controllers\Api\Listing\Price;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\Price\CreateListingPriceRequest;
use App\Http\Requests\Listing\Price\EditListingPriceRequest;
use App\Http\Resources\Listing\ListingPriceResource;
use App\Http\Resources\PriceResource;
use App\Models\Price;
use App\Models\Listing;
use App\Repositories\ListingPriceRepository;
use App\Services\Listing\ListingPriceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingPriceController extends Controller
{

    public function __construct(
        private ListingPriceService $listingPriceService,
        private ListingPriceRepository $listingPriceRepository,
    ) {}

    public function index(Listing $listing, Request $request) {
        $this->listingPriceRepository->setQuery(
            $listing->prices()
        );
        $this->listingPriceRepository->setPagination(true);
        $this->listingPriceRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->listingPriceRepository->setOrderDir(
            $request->get('order', 'asc')
        );
        $this->listingPriceRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingPriceRepository->setPage(
            $request->get('page', 1)
        );

        return PriceResource::collection(
            $this->listingPriceRepository->findMany()
        );
    }

    public function view(Listing $listing, Price $price, Request $request) {
        $this->listingPriceService->setUser($request->user()->user);
        $this->listingPriceService->setSite($request->user()->site);

        return new ListingPriceResource($price);
    }

    public function create(Listing $listing, CreateListingPriceRequest $request)
    {
        $this->listingPriceService->setUser($request->user()->user);
        $this->listingPriceService->setSite($request->user()->site);

        if (
            !$this->listingPriceService->createlistingPrice(
                $listing,
                $request->validated(),
            )
        ) {
            return response()->json([
                'message' => 'Error adding listing price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Added listing price',
        ], Response::HTTP_CREATED);
    }

    public function update(Listing $listing, Price $price, EditListingPriceRequest $request)
    {
        $this->listingPriceService->setUser($request->user()->user);
        $this->listingPriceService->setSite($request->user()->site);

        if (
            !$this->listingPriceService->updatelistingPrice(
                $listing,
                $price,
                $request->validated()
            )
        ) {
            return response()->json([
                'message' => 'Error updating listing price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Updated listing price',
        ], Response::HTTP_OK);
    }

    public function destroy(Listing $listing, Price $price, Request $request)
    {
        $this->listingPriceService->setUser($request->user()->user);
        $this->listingPriceService->setSite($request->user()->site);

        if (
            !$this->listingPriceService->deletelistingPrice(
                $listing,
                $price
            )
        ) {
            return response()->json([
                'message' => 'Error deleting listing price',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Deleted listing price',
        ], Response::HTTP_OK);
    }
}
