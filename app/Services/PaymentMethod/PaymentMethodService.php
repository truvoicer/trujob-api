<?php

namespace App\Services\PaymentMethod;

use App\Models\PaymentMethod;
use App\Services\BaseService;

class PaymentMethodService extends BaseService
{

    public function createPaymentMethod(array $data) {
        $paymentMethod = new PaymentMethod($data);
        if (!$paymentMethod->save()) {
            throw new \Exception('Error creating product paymentMethod');
        }
        return true;
    }
    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data) {
        if (!$paymentMethod->update($data)) {
            throw new \Exception('Error updating product paymentMethod');
        }
        return true;
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod) {
        if (!$paymentMethod->delete()) {
            throw new \Exception('Error deleting product paymentMethod');
        }
        return true;
    }

}
