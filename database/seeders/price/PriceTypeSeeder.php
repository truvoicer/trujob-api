<?php
namespace Database\Seeders\price;

use App\Services\PriceType\PriceTypeService;
use Illuminate\Database\Seeder;

class PriceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(PriceTypeService $priceTypeService)
    {
        $priceTypeService->defaultPriceTypes();
    }
}