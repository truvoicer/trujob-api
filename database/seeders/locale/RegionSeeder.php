<?php
// database/seeders/RegionSeeder.php

namespace Database\Seeders\locale;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting region seeding process...');
        
        // Get all countries from database
        $countries = Country::all();
        $totalCountries = count($countries);
        $processedCountries = 0;

        foreach ($countries as $country) {
            $processedCountries++;
            $this->command->info("\nProcessing {$country->name} ({$processedCountries}/{$totalCountries})");

                $this->seedFallbackRegions($country);
            // Try API first
            $apiSuccess = $this->seedRegionsFromApi($country);
            
            // Fallback to local data if API fails
            if (!$apiSuccess) {
                $this->seedFallbackRegions($country);
            }
        }

        $this->command->info("\nRegion seeding completed!");
    }

    protected function seedRegionsFromApi(Country $country): bool
    {
        try {
            $this->command->info("Fetching regions from API for {$country->name}...");
            
            $regions = $this->fetchRegionsFromGeoNames($country->iso2);

            if (empty($regions)) {
                $this->command->warn("No regions found in API for {$country->name}");
                return false;
            }

            $this->processRegions($regions, $country);
            return true;

        } catch (\Exception $e) {
            Log::error("API failed for {$country->name}: " . $e->getMessage());
            $this->command->error("API request failed for {$country->name}");
            return false;
        }
    }

    protected function fetchRegionsFromGeoNames(string $countryCode): array
    {
        $username = config('services.geonames.username', 'demo'); // Register for free at geonames.org
        
        $response = Http::get("http://api.geonames.org/childrenJSON", [
            'geonameId' => $this->getCountryGeonameId($countryCode),
            'username' => $username,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['geonames'] ?? [];
        }

        return [];
    }

    protected function getCountryGeonameId(string $countryCode): ?int
    {
        // Mapping of country codes to GeoNames IDs
        $countryGeonameIds = [
            'US' => 6252001, // United States
            'CA' => 6251999, // Canada
            'GB' => 2635167, // United Kingdom
            'DE' => 2921044, // Germany
            'FR' => 3017382, // France
            // Add more country mappings as needed
        ];

        return $countryGeonameIds[$countryCode] ?? null;
    }

    protected function processRegions(array $apiRegions, Country $country): void
    {
        $bar = $this->command->getOutput()->createProgressBar(count($apiRegions));
        $bar->setMessage("Processing regions for {$country->name}");
        $bar->start();

        $batch = [];
        $batchSize = 50;

        foreach ($apiRegions as $region) {
            $regionData = $this->transformRegionData($region, $country);

            if ($regionData) {
                $batch[] = $regionData;

                if (count($batch) >= $batchSize) {
                    Region::insert($batch);
                    $batch = [];
                }
            }

            $bar->advance();
        }

        // Insert remaining records
        if (!empty($batch)) {
            Region::insert($batch);
        }

        $bar->finish();
        $this->command->info(" - Added " . count($apiRegions) . " regions for {$country->name}");
    }

    protected function transformRegionData(array $apiRegion, Country $country): ?array
    {
        if (empty($apiRegion['name'])) {
            return null;
        }

        return [
            'country_id' => $country->id,
            'name' => $apiRegion['name'],
            'code' => $apiRegion['adminCode1'] ?? substr($apiRegion['name'], 0, 3),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function seedFallbackRegions(Country $country)
    {
        $fallbackRegions = $this->getAllFallbackRegions($country->iso2);

        if (empty($fallbackRegions)) {
            $this->command->warn("No fallback regions available for {$country->name}");
            return;
        }

        $this->command->info("Using fallback data for {$country->name}...");

        $bar = $this->command->getOutput()->createProgressBar(count($fallbackRegions));
        $bar->start();

        foreach ($fallbackRegions as $region) {
            Region::updateOrCreate(
                [
                    'country_id' => $country->id,
                    'code' => $region['code'],
                ],
                [
                    'name' => $region['name'],
                    'is_active' => true,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->command->info(" - Added " . count($fallbackRegions) . " fallback regions for {$country->name}");
    }

    protected function getAllFallbackRegions(string $countryCode): array
    {
        // Comprehensive fallback data for all countries
        $fallbackData = [
            // United States
            'US' => array_map(function($state) {
                return ['code' => $state[0], 'name' => $state[1]];
            }, [
                ['AL', 'Alabama'], ['AK', 'Alaska'], ['AZ', 'Arizona'], ['AR', 'Arkansas'],
                ['CA', 'California'], ['CO', 'Colorado'], ['CT', 'Connecticut'], ['DE', 'Delaware'],
                ['FL', 'Florida'], ['GA', 'Georgia'], ['HI', 'Hawaii'], ['ID', 'Idaho'],
                ['IL', 'Illinois'], ['IN', 'Indiana'], ['IA', 'Iowa'], ['KS', 'Kansas'],
                ['KY', 'Kentucky'], ['LA', 'Louisiana'], ['ME', 'Maine'], ['MD', 'Maryland'],
                ['MA', 'Massachusetts'], ['MI', 'Michigan'], ['MN', 'Minnesota'], ['MS', 'Mississippi'],
                ['MO', 'Missouri'], ['MT', 'Montana'], ['NE', 'Nebraska'], ['NV', 'Nevada'],
                ['NH', 'New Hampshire'], ['NJ', 'New Jersey'], ['NM', 'New Mexico'], ['NY', 'New York'],
                ['NC', 'North Carolina'], ['ND', 'North Dakota'], ['OH', 'Ohio'], ['OK', 'Oklahoma'],
                ['OR', 'Oregon'], ['PA', 'Pennsylvania'], ['RI', 'Rhode Island'], ['SC', 'South Carolina'],
                ['SD', 'South Dakota'], ['TN', 'Tennessee'], ['TX', 'Texas'], ['UT', 'Utah'],
                ['VT', 'Vermont'], ['VA', 'Virginia'], ['WA', 'Washington'], ['WV', 'West Virginia'],
                ['WI', 'Wisconsin'], ['WY', 'Wyoming'], ['DC', 'District of Columbia'],
                ['AS', 'American Samoa'], ['GU', 'Guam'], ['MP', 'Northern Mariana Islands'],
                ['PR', 'Puerto Rico'], ['UM', 'United States Minor Outlying Islands'], ['VI', 'Virgin Islands']
            ]),

            // Canada
            'CA' => array_map(function($province) {
                return ['code' => $province[0], 'name' => $province[1]];
            }, [
                ['AB', 'Alberta'], ['BC', 'British Columbia'], ['MB', 'Manitoba'], ['NB', 'New Brunswick'],
                ['NL', 'Newfoundland and Labrador'], ['NT', 'Northwest Territories'], ['NS', 'Nova Scotia'],
                ['NU', 'Nunavut'], ['ON', 'Ontario'], ['PE', 'Prince Edward Island'], ['QC', 'Quebec'],
                ['SK', 'Saskatchewan'], ['YT', 'Yukon']
            ]),

            // United Kingdom
            'GB' => [
                ['code' => 'ENG', 'name' => 'England'],
                ['code' => 'SCT', 'name' => 'Scotland'],
                ['code' => 'WLS', 'name' => 'Wales'],
                ['code' => 'NIR', 'name' => 'Northern Ireland']
            ],

            // Germany
            'DE' => array_map(function($state) {
                return ['code' => $state[0], 'name' => $state[1]];
            }, [
                ['BW', 'Baden-Württemberg'], ['BY', 'Bavaria'], ['BE', 'Berlin'], ['BB', 'Brandenburg'],
                ['HB', 'Bremen'], ['HH', 'Hamburg'], ['HE', 'Hesse'], ['MV', 'Mecklenburg-Vorpommern'],
                ['NI', 'Lower Saxony'], ['NW', 'North Rhine-Westphalia'], ['RP', 'Rhineland-Palatinate'],
                ['SL', 'Saarland'], ['SN', 'Saxony'], ['ST', 'Saxony-Anhalt'], ['SH', 'Schleswig-Holstein'],
                ['TH', 'Thuringia']
            ]),

            // France
            'FR' => array_map(function($region) {
                return ['code' => $region[0], 'name' => $region[1]];
            }, [
                ['ARA', 'Auvergne-Rhône-Alpes'], ['BFC', 'Bourgogne-Franche-Comté'], ['BRE', 'Brittany'],
                ['CVL', 'Centre-Val de Loire'], ['COR', 'Corsica'], ['GES', 'Grand Est'],
                ['HDF', 'Hauts-de-France'], ['IDF', 'Île-de-France'], ['NOR', 'Normandy'],
                ['NAQ', 'Nouvelle-Aquitaine'], ['OCC', 'Occitanie'], ['PDL', 'Pays de la Loire'],
                ['PAC', 'Provence-Alpes-Côte d\'Azur']
            ]),

            // Australia
            'AU' => array_map(function($state) {
                return ['code' => $state[0], 'name' => $state[1]];
            }, [
                ['ACT', 'Australian Capital Territory'], ['NSW', 'New South Wales'], ['NT', 'Northern Territory'],
                ['QLD', 'Queensland'], ['SA', 'South Australia'], ['TAS', 'Tasmania'],
                ['VIC', 'Victoria'], ['WA', 'Western Australia']
            ]),

            // Add more countries as needed...
            'JP' => array_map(function($prefecture) {
                return ['code' => $prefecture[0], 'name' => $prefecture[1]];
            }, [
                ['01', 'Hokkaido'], ['02', 'Aomori'], ['03', 'Iwate'], ['04', 'Miyagi'],
                // ... all 47 prefectures ...
                ['47', 'Okinawa']
            ]),

            // For countries without regions, we'll use the country itself as a single region
            // This pattern can be applied to small countries or city-states
            'SG' => [['code' => 'SG', 'name' => 'Singapore']],
            'MC' => [['code' => 'MC', 'name' => 'Monaco']],
            'VA' => [['code' => 'VA', 'name' => 'Vatican City']],
        ];

        // Default fallback - use country as single region if no specific data exists
        if (!isset($fallbackData[$countryCode])) {
            return [['code' => $countryCode, 'name' => 'All Regions']];
        }

        return $fallbackData[$countryCode];
    }
}