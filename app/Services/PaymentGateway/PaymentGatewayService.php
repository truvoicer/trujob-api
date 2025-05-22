<?php

namespace App\Services\PaymentGateway;

use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Models\PaymentGateway;
use App\Services\BaseService;

class PaymentGatewayService extends BaseService
{

    public function createPaymentGateway(array $data) {
        $paymentGateway = new PaymentGateway($data);
        if (!$paymentGateway->save()) {
            throw new \Exception('Error creating listing paymentGateway');
        }
        return true;
    }
    public function updatePaymentGateway(PaymentGateway $paymentGateway, array $data) {
        if (!$paymentGateway->update($data)) {
            throw new \Exception('Error updating listing paymentGateway');
        }
        return true;
    }

    public function deletePaymentGateway(PaymentGateway $paymentGateway) {
        if (!$paymentGateway->delete()) {
            throw new \Exception('Error deleting listing paymentGateway');
        }
        return true;
    }

    public function seed() {
        foreach (PaymentPaymentGateway::cases() as $gateway) {
            $paymentGateway = new PaymentGateway([
                'name' => $gateway->value,
            ]);
            if (!$paymentGateway->save()) {
                throw new \Exception('Error seeding paymentGateway');
            }
        }
    }
}
