<?php

namespace Database\Seeders\locale;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CountrySeeder extends Seeder
{
 public function run()
    {
        // Fetch country data from a reliable API
        $response = Http::get('https://restcountries.com/v3.1/all?fields=name,cca2,cca3,idd');
        $countries = $response->json();

        foreach ($countries as $country) {
            Country::create([
                'name' => $country['name']['common'] ?? null,
                'iso2' => $country['cca2'] ?? null,
                'iso3' => $country['cca3'] ?? null,
                'phone_code' => $this->extractPhoneCode($country),
            ]);
        }

        // Add any missing countries manually if needed
        $this->addManualCountries();
    }

    protected function extractPhoneCode(array $country): ?string
    {
        if (!isset($country['idd']['root'])) {
            return null;
        }

        $root = $country['idd']['root'];
        $suffixes = $country['idd']['suffixes'] ?? [''];
        
        // Take the first suffix if available
        return $root . ($suffixes[0] ?? '');
    }

    protected function addManualCountries()
    {
        $manualCountries = [
            [
                'name' => 'Kosovo',
                'iso2' => 'XK',
                'iso3' => 'XKX',
                'phone_code' => '383',
            ],
            // Add other missing countries as needed
        ];

        foreach ($manualCountries as $country) {
            if (!Country::where('iso2', $country['iso2'])->exists()) {
                Country::create($country);
            }
        }
    }
}
