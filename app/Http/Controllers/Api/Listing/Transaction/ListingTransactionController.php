<?php

namespace App\Http\Controllers\Api\Listing\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Listing\StoreListingTransactionRequest;
use App\Http\Requests\Listing\UpdateListingTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Listing;
use App\Models\Transaction;
use App\Repositories\ListingRepository;
use App\Services\Listing\ListingTransactionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ListingTransactionController extends Controller
{

    public function __construct(
        private ListingTransactionService $transactionService,
        private ListingRepository $listingRepository,
    )
    {}


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Listing $listing, Request $request) {
        $this->listingRepository->setQuery(
            $listing->transactions()
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

        return TransactionResource::collection(
            $this->listingRepository->findMany()
        );
    }

    public function view(Listing $listing, Transaction $transaction, Request $request) {
        $this->transactionService->setUser($request->user()->user);
        $this->transactionService->setSite($request->user()->site);
        $check = $listing->transactions()->where('transactions.id', $transaction->id)->first();
        if (!$check) {
            return response()->json([
                'message' => 'Transaction not found in listing',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return new TransactionResource($transaction);
    }

    public function create(Listing $listing, StoreListingTransactionRequest $request) {
        $this->transactionService->setUser($request->user()->user);
        $this->transactionService->setSite($request->user()->site);

        if (!$this->transactionService->createListingTransaction($listing, $request->validated())) {
            return response()->json([
                'message' => 'Error creating listing transaction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing transaction created',
        ], Response::HTTP_CREATED);
    }

    
    public function update(Listing $listing, Transaction $transaction, UpdateListingTransactionRequest $request) {
        $this->transactionService->setUser($request->user()->user);
        $this->transactionService->setSite($request->user()->site);

        if (!$this->transactionService->updateListingTransaction($listing, $transaction, $request->validated())) {
            return response()->json([
                'message' => 'Error updating listing transaction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing transaction updated',
        ], Response::HTTP_OK);
    }
    
    public function destroy(Listing $listing, Transaction $transaction, Request $request) {
        $this->transactionService->setUser($request->user()->user);
        $this->transactionService->setSite($request->user()->site);
        
        if (!$this->transactionService->deleteListingTransaction($listing, $transaction)) {
            return response()->json([
                'message' => 'Error deleting listing transaction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Listing transaction deleted',
        ], Response::HTTP_OK);
    }
}
