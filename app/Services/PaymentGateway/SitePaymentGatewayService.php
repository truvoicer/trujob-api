<?php

namespace App\Services\PaymentGateway;

use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Models\PaymentGateway;
use App\Services\BaseService;
use Faker\Provider\ar_EG\Payment;

class SitePaymentGatewayService extends BaseService
{

    public function buildSettings(PaymentGateway $paymentGateway, array $data): array
    {
        $data['settings'] = array_merge(
            $paymentGateway?->pivot->settings ?? [],
            (!empty($data['settings']) && is_array($data['settings']))
                ? $data['settings']
                : []
        );
        return $data;
    }

    public function createPaymentGateway(PaymentGateway $paymentGateway, array $data)
    {
        $findPaymentGateway = $this->site->paymentGateways()->find($paymentGateway->id);
        if ($findPaymentGateway) {
            throw new \Exception('Payment gateway already exists for this site');
        }

        $this->site->paymentGateways()->attach($paymentGateway->id, $data);

        return true;
    }
    public function updatePaymentGateway(PaymentGateway $paymentGateway, array $data)
    {
        $this->site->paymentGateways()->updateExistingPivot(
            $paymentGateway->id,
            $this->buildSettings($paymentGateway, $data)
        );
        return true;
    }


    public function savePaymentGateway(PaymentGateway $paymentGateway, array $data)
    {

        $findPaymentGateway = $this->site->paymentGateways()->find($paymentGateway->id);

        if (!$findPaymentGateway) {
            $this->createPaymentGateway($paymentGateway, $data);
            return true;
        }

        $this->updatePaymentGateway($findPaymentGateway, $data);
        return true;
    }

    public function deletePaymentGateway(PaymentGateway $paymentGateway)
    {
        if (!$this->site->paymentGateways()->detach($paymentGateway)) {
            throw new \Exception('Error deleting product paymentGateway');
        }
        return true;
    }
}
