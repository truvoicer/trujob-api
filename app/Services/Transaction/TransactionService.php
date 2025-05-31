<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Services\BaseService;

class TransactionService extends BaseService
{
    public function createTransaction(array $data) {
        $transaction = new Transaction($data);
        if (!$transaction->save()) {
            throw new \Exception('Error creating product transaction');
        }
        return $transaction;
    }
    public function updateTransaction(Transaction $transaction, array $data) {
        if (!$transaction->update($data)) {
            throw new \Exception('Error updating product transaction');
        }
        return $transaction;
    }

    public function deleteTransaction(Transaction $transaction) {
        if (!$transaction->delete()) {
            throw new \Exception('Error deleting product transaction');
        }
        return true;
    }

}
