<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Language; // Make sure to import your Language model
use App\Models\Country; // Import Country model if you plan to link later

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $response = Http::get('https://restcountries.com/v3.1/all?fields=languages,name,cca2,cca3');

        if ($response->successful()) {
            $countriesData = $response->json();

            foreach ($countriesData as $countryData) {
                $countryIso2 = $countryData['cca2'] ?? null;

                if (!$countryIso2) {
                    continue;
                } // Skip countries without ISO2 code
                $country = Country::where('iso2', strtoupper($countryIso2))->first();
                if (!$country) {
                    continue; // Skip if country not found
                }
                if (!isset($countryData['languages']) || !is_array($countryData['languages'])) {
                    continue; // Skip countries without languages
                }
                foreach ($countryData['languages'] as $iso639_2 => $languageName) {
                    $country->languages()->updateOrCreate(
                        ['iso639_2' => $iso639_2],
                        ['name' => $languageName, 'iso639_2' => $iso639_2] // Assuming iso639_2 is the same as iso639_1 for simplicity
                    );
                }
            }

            $this->command->info('Languages seeded successfully!');

        } else {
            $this->command->error('Failed to fetch languages from API. Status: ' . $response->status());
        }
    }
}
