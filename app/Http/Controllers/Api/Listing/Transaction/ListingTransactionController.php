<?php

namespace App\Http\Controllers\Api\Listing\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingTransactionRequest;
use App\Http\Requests\Listing\UpdateListingTransactionRequest;
use App\Http\Resources\Listing\ListingTransactionResource;
use App\Models\Listing;
use App\Models\ListingTransaction;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingTransactionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingTransactionController extends Controller
{

    public function __construct(
        private ListingTransactionService $listingTransactionService,
        private ListingRepository $listingRepository,
    )
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Listing $listing, Request $request) {
        $this->listingRepository->setQuery(
            $listing->listingTransaction()
        );
        $this->listingRepository->setPagination(true);
        $this->listingRepository->setSortField(
            $request->get('sort', 'created_at')
        );
        $this->listingRepository->setOrderDir(
            $request->get('order', 'desc')
        );
        $this->listingRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->listingRepository->setPage(
            $request->get('page', 1)
        );

        return ListingTransactionResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function create(Listing $listing, StoreListingTransactionRequest $request) {
        $this->listingTransactionService->setUser($request->user()->user);
        $this->listingTransactionService->setSite($request->user()->site);

        if (!$this->listingTransactionService->createListingTransaction($listing, $request->validated())) {
            return response()->json([
                'message' => 'Error creating listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review created',
        ], Response::HTTP_CREATED);
    }
    
    public function update(Listing $listing, ListingTransaction $listingTransaction, UpdateListingTransactionRequest $request) {
        $this->listingTransactionService->setUser($request->user()->user);
        $this->listingTransactionService->setSite($request->user()->site);

        if (!$this->listingTransactionService->updateListingTransaction($listingTransaction, $request->validated())) {
            return response()->json([
                'message' => 'Error updating listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review updated',
        ], Response::HTTP_OK);
    }
    public function destroy(Listing $listing, ListingTransaction $listingTransaction, Request $request) {
        $this->listingTransactionService->setUser($request->user()->user);
        $this->listingTransactionService->setSite($request->user()->site);

        if (!$this->listingTransactionService->deleteListingTransaction($listingTransaction)) {
            return response()->json([
                'message' => 'Error deleting listing review',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing review deleted',
        ], Response::HTTP_OK);
    }
}
