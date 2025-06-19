<?php
namespace Database\Seeders\discount;

use App\Services\Discount\DiscountService;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(DiscountService $discountService)
    {
        $discounts = include(database_path('data/DiscountData.php'));
        if (!$discounts) {
            throw new \Exception('Error reading DiscountData.php file ' . database_path('data/DiscountData.php'));
        }
        foreach ($discounts as $discount) {
            $discountService->createDiscount($discount);
        }
    }

}
