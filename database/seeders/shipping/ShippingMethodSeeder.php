<?php
namespace Database\Seeders\tax;

use App\Services\Shipping\ShippingMethodService;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(ShippingMethodService $shippingMethodService)
    {
        $shippingMethods = include(database_path('data/ShippingMethodData.php'));
        if (!$shippingMethods) {
            throw new \Exception('Error reading ShippingMethodData.php file ' . database_path('data/ShippingMethodData.php'));
        }
        foreach ($shippingMethods as $shippingMethod) {
            $shippingMethodService->createShippingMethod($shippingMethod);
        }
    }

}
