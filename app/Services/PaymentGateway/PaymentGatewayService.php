<?php

namespace App\Services\PaymentGateway;

use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Models\PaymentGateway;
use App\Services\BaseService;

class PaymentGatewayService extends BaseService
{

    public function createPaymentGateway(array $data)
    {
        $paymentGateway = new PaymentGateway($data);
        if (!$paymentGateway->save()) {
            throw new \Exception('Error creating product paymentGateway');
        }
        return true;
    }
    public function updatePaymentGateway(PaymentGateway $paymentGateway, array $data)
    {
        if (!$paymentGateway->update($data)) {
            throw new \Exception('Error updating product paymentGateway');
        }
        return true;
    }

    public function deletePaymentGateway(PaymentGateway $paymentGateway)
    {
        if (!$paymentGateway->delete()) {
            throw new \Exception('Error deleting product paymentGateway');
        }
        return true;
    }

    public function seed()
    {
        foreach (PaymentPaymentGateway::cases() as $gateway) {
            $requiredFieldsPath = storage_path('app/private/payment-gateway/' . $gateway->value . '/required-fields.json');
            if (file_exists($requiredFieldsPath)) {
                $requiredFields = json_decode(file_get_contents($requiredFieldsPath), true);
                if (!$requiredFields) {
                    throw new \Exception('Invalid required fields for payment gateway: ' . $gateway->value);
                }
            } else {
                $requiredFields = [];
            }
            $paymentGateway = new PaymentGateway();

            if (!$paymentGateway->updateOrInsert([
                'name' => $gateway->value,
            ], [
                'name' => $gateway->value,
                'label' => $gateway->label(),
                'description' => $gateway->description(),
                'is_active' => true,
                'is_default' => $gateway->isDefault(),
                'required_fields' => $requiredFields,
            ])) {
                throw new \Exception('Error seeding paymentGateway');
            }
        }
    }
}
