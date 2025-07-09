<?php

namespace App\Services\Order\Transaction;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\BaseService;

class OrderTransactionService extends BaseService
{
    public function createTransaction(Order $order, array $data) {
        $transaction = new Transaction($data);
        $order->transactions()->save($transaction);
        return $transaction;
    }
    public function updateTransaction(Order $order, Transaction $transaction, array $data) {
        if (!$transaction->update($data)) {
            throw new \Exception('Error updating order transaction');
        }
        return $transaction;
    }

    public function deleteTransaction(Order $order, Transaction $transaction) {
        if (!$transaction->delete()) {
            throw new \Exception('Error deleting order transaction');
        }
        return true;
    }

}
