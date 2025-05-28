<?php

namespace Database\Seeders\locale;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{

    public function run()
    {

        // Fetch currency data from a reliable API
        $response = Http::get('https://restcountries.com/v3.1/all?fields=cca2,currencies');
        $countries = $response->json();

        foreach ($countries as $countryData) {
            if (!isset($countryData['currencies'])) {
                continue;
            }

            $country = Country::where('iso2', $countryData['cca2'])->first();
            if (!$country) {
                continue;
            }

            foreach ($countryData['currencies'] as $currencyCode => $currencyInfo) {
                Currency::create([
                    'country_id' => $country->id,
                    'name' => $currencyInfo['name'] ?? $currencyCode,
                    'name_plural' => $currencyInfo['name'] ?? $currencyCode,
                    'code' => $currencyCode,
                    'symbol' => $currencyInfo['symbol'] ?? $currencyCode,
                ]);
            }
        }

        // Add any missing currencies manually if needed
        $this->addManualCurrencies();
    }

    protected function addManualCurrencies()
    {

        $manualCurrencies = [
            // [
            //     'country_id' => Country::where('iso2', 'US')->first()->id,
            //     'name' => 'US Dollar',
            //     'name_plural' => 'US Dollars',
            //     'code' => 'USD',
            //     'symbol' => '$',
            // ],
            // [
            //     'country_id' => Country::where('iso2', 'EU')->first()->id,
            //     'name' => 'Euro',
            //     'name_plural' => 'Euros',
            //     'code' => 'EUR',
            //     'symbol' => '€',
            // ],
            // Add other missing currencies as needed
        ];

        foreach ($manualCurrencies as $currency) {
            if (!Currency::where('code', $currency['code'])->exists()) {
                Currency::create($currency);
            }
        }
    }
}
