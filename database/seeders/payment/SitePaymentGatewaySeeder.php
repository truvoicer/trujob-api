<?php
namespace Database\Seeders\payment;

use App\Enums\Payment\PaymentGateway;
use App\Enums\Payment\PaymentGatewayEnvironment;
use App\Models\PaymentGateway as ModelsPaymentGateway;
use App\Models\Site;
use App\Services\PaymentGateway\SitePaymentGatewayService;
use Illuminate\Database\Seeder;

class SitePaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(SitePaymentGatewayService $sitePaymentGatewayService)
    {
        foreach (Site::all() as $site) {
            $sitePaymentGatewayService->setSite($site);
            foreach (PaymentGateway::cases() as $gateway) {
                $findPaymentGateway = ModelsPaymentGateway::where('name', $gateway->value)->first();
                if (!$findPaymentGateway) {
                    throw new \Exception('Payment gateway not found: ' . $gateway->value);
                }
                $crendentialsPath = storage_path('app/private/payment-gateway/' . $gateway->value . '/credentials.json');
                if (!file_exists($crendentialsPath)) {
                    continue; // Skip if credentials file does not exist
                }
                $credentials = json_decode(file_get_contents($crendentialsPath), true);
                if (!$credentials) {
                    throw new \Exception('Invalid credentials for payment gateway: ' . $gateway->value);
                }
                $sitePaymentGatewayService->savePaymentGateway(
                    $findPaymentGateway,
                    [
                        'is_active' => true,
                        'is_default' => false,
                        'environment' =>  PaymentGatewayEnvironment::SANDBOX->value,
                        'settings' => $credentials
                    ]
                );
            }
        }
    }

}
