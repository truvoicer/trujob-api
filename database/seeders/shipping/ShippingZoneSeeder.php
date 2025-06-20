<?php
namespace Database\Seeders\shipping;

use App\Services\Shipping\ShippingZoneService;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(ShippingZoneService $shippingZoneService)
    {
        $shippingZones = include(database_path('data/ShippingZoneData.php'));
        if (!$shippingZones) {
            throw new \Exception('Error reading ShippingZoneData.php file ' . database_path('data/ShippingZoneData.php'));
        }
        foreach ($shippingZones as $shippingZone) {
            $shippingZoneService->createShippingZone($shippingZone);
        }
    }

}
