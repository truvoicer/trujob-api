<?php
namespace Database\Seeders\payment;

use App\Services\PaymentGateway\PaymentGatewayService;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(PaymentGatewayService $paymentGatewaySeeder)
    {
        $paymentGatewaySeeder->seed();
    }
    
}