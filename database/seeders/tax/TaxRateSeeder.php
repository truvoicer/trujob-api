<?php
namespace Database\Seeders\tax;

use App\Services\Tax\TaxRateService;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(TaxRateService $taxRateService)
    {
        $taxRates = include(database_path('data/TaxRateData.php'));
        if (!$taxRates) {
            throw new \Exception('Error reading TaxRateData.php file ' . database_path('data/TaxRateData.php'));
        }
        foreach ($taxRates as $taxRate) {
            $taxRateService->createTaxRate($taxRate);
        }
    }

}
