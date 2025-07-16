<?php

namespace App\Services\Order\Transaction;

use App\Enums\Transaction\TransactionStatus;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\BaseService;

class OrderTransactionService extends BaseService
{
    public function createTransaction(Order $order, array $data): Transaction {

        $data['currency_code'] = $order->currency?->code ?? null;
        $data['status'] = TransactionStatus::PENDING->value;
        $data['order_id'] = $order->id;
        return $order->transactions()->create($data);
    }
    public function updateTransaction(Order $order, Transaction $transaction, array $data): Transaction {
        if (!$transaction->update($data)) {
            throw new \Exception('Error updating order transaction');
        }
        return $transaction;
    }

    public function deleteTransaction(Order $order, Transaction $transaction): bool
    {
        if (!$transaction->delete()) {
            throw new \Exception('Error deleting order transaction');
        }
        return true;
    }

}
