<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Services\BaseService;

class OrderService extends BaseService
{
    public function createOrder(array $data) {
        $transaction = new Order($data);
        if (!$transaction->save()) {
            throw new \Exception('Error creating listing transaction');
        }
        return $transaction;
    }
    public function updateOrder(Order $transaction, array $data) {
        if (!$transaction->update($data)) {
            throw new \Exception('Error updating listing transaction');
        }
        return $transaction;
    }

    public function deleteOrder(Order $transaction) {
        if (!$transaction->delete()) {
            throw new \Exception('Error deleting listing transaction');
        }
        return true;
    }

}
