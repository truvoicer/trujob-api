<?php

namespace App\Http\Controllers\Api\Order\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\Transaction\StoreOrderTransactionRequest;
use App\Http\Requests\Order\Transaction\UpdateOrderTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Order;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\Order\Transaction\OrderTransactionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderTransactionController extends Controller
{

    public function __construct(
        private OrderTransactionService $orderTransactionService,
        private TransactionRepository $transactionRepository,
    ) {
        parent::__construct();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order, Request $request)
    {
        $this->transactionRepository->setQuery(
            $order->transactions()
        );
        $this->transactionRepository->setPagination(true);
        $this->transactionRepository->setOrderByColumn(
            $request->get('sort', 'created_at')
        );
        $this->transactionRepository->setOrderByDir(
            $request->get('order', 'desc')
        );
        $this->transactionRepository->setPerPage(
            $request->get('per_page', 10)
        );
        $this->transactionRepository->setPage(
            $request->get('page', 1)
        );

        return TransactionResource::collection(
            $this->transactionRepository->findMany()
        );
    }

    public function show(Order $order, Transaction $transaction, Request $request)
    {
        $this->orderTransactionService->setUser($request->user()->user);
        $this->orderTransactionService->setSite($request->user()->site);

        return new TransactionResource($transaction);
    }

    public function store(Order $order, StoreOrderTransactionRequest $request)
    {
        $this->orderTransactionService->setUser($request->user()->user);
        $this->orderTransactionService->setSite($request->user()->site);

        if (!$this->orderTransactionService->createTransaction(
            $order,
            $request->validated()
        )) {
            return response()->json(
                $this->responseHelpers->encryptedResponse([
                    'message' => 'Error creating transaction'
                ]), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json(
            $this->responseHelpers->encryptedResponse([
                'message' => 'Transaction created'
            ]), Response::HTTP_CREATED);
    }


    public function update(Order $order, Transaction $transaction, UpdateOrderTransactionRequest $request)
    {
        $this->orderTransactionService->setUser($request->user()->user);
        $this->orderTransactionService->setSite($request->user()->site);

        if (!$this->orderTransactionService->updateTransaction(
            $order,
            $transaction,
            $request->validated()
        )) {
            return response()->json([
                'message' => 'Error updating transaction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Transaction updated',
        ], Response::HTTP_OK);
    }

    public function destroy(Order $order, Transaction $transaction, Request $request)
    {
        $this->orderTransactionService->setUser($request->user()->user);
        $this->orderTransactionService->setSite($request->user()->site);

        if (!$this->orderTransactionService->deleteTransaction(
            $order,
            $transaction
        )) {
            return response()->json([
                'message' => 'Error deleting transaction',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return response()->json([
            'message' => 'Transaction deleted',
        ], Response::HTTP_OK);
    }
}
