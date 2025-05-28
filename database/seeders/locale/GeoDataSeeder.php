<?php

namespace Database\Seeders\locale;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GeoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder fetches country, currency, and region data from external APIs
     * (restcountries.com) and populates the 'countries', 'currencies', and 'regions'
     * tables in your database.
     *
     * Before running:
     * - Ensure you have the Guzzle HTTP client installed (usually comes with Laravel).
     * - Make sure your database connection is configured correctly.
     *
     * @return void
     */
    public function run()
    {
        // Temporarily disable foreign key checks to allow truncation of tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate existing data in the tables to ensure a clean seed
        $this->command->info('Truncating existing data in countries, currencies, and regions tables...');
        DB::table('countries')->truncate();
        DB::table('currencies')->truncate();
        DB::table('regions')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Fetching countries data from restcountries.com...');

        // Fetch all countries data from restcountries.com API
        // We select specific fields to optimize the response size
        $response = Http::get('https://restcountries.com/v3.1/all?fields=name,cca2,cca3,idd,currencies,region,subregion');

        // Check if the API request failed
        if ($response->failed()) {
            $this->command->error('Failed to fetch countries data from restcountries.com. Please check your internet connection or the API endpoint.');
            return;
        }

        $countriesData = $response->json();

        $this->command->info('Populating countries, currencies, and regions tables...');

        // Iterate through each country fetched from the API
        foreach ($countriesData as $countryData) {
            // Extract country details
            $countryName = $countryData['name']['common'] ?? null;
            $iso2 = $countryData['cca2'] ?? null;
            $iso3 = $countryData['cca3'] ?? null;

            // Construct phone code from 'idd' field.
            // 'idd' contains 'root' (e.g., "+1") and 'suffixes' (e.g., ["2", "3"]).
            // We concatenate root with the first suffix if available.
            $phoneCode = null;
            if (isset($countryData['idd']['root'])) {
                $phoneCode = $countryData['idd']['root'];
                if (isset($countryData['idd']['suffixes']) && count($countryData['idd']['suffixes']) > 0) {
                    $phoneCode .= $countryData['idd']['suffixes'][0];
                }
            }

            // Skip country if essential data is missing
            if (!$countryName || !$iso2 || !$iso3) {
                $this->command->warn("Skipping country due to missing essential data: " . json_encode($countryData['name'] ?? 'N/A'));
                continue;
            }

            // Insert country into the 'countries' table
            // insertGetId returns the ID of the newly inserted record
            $countryId = DB::table('countries')->insertGetId([
                'name' => $countryName,
                'iso2' => $iso2,
                'iso3' => $iso3,
                'phone_code' => $phoneCode,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert currencies related to the current country
            if (isset($countryData['currencies']) && is_array($countryData['currencies'])) {
                foreach ($countryData['currencies'] as $currencyCode => $currencyDetails) {
                    // Insert currency into the 'currencies' table
                    DB::table('currencies')->insert([
                        'country_id' => $countryId,
                        'name' => $currencyDetails['name'] ?? $currencyCode,
                        // restcountries.com does not provide a plural name, so we use the singular name as a fallback.
                        'name_plural' => $currencyDetails['name'] ?? $currencyCode,
                        'code' => $currencyCode,
                        'symbol' => $currencyDetails['symbol'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Insert regions related to the current country
            // We use 'region' and 'subregion' from restcountries.com as regions.
            // The schema implies more granular regions (like states/provinces),
            // but this API provides broader geographical regions.
            $regionsToInsert = [];

            // Add the main region if it exists
            if (isset($countryData['region']) && !empty($countryData['region'])) {
                $regionName = $countryData['region'];
                $regionCode = Str::slug($regionName); // Generate a slug for the 'code' column
                $regionsToInsert[$regionCode] = $regionName; // Use code as key to ensure uniqueness within this loop
            }

            // Add the subregion if it exists and is different from the main region
            if (isset($countryData['subregion']) && !empty($countryData['subregion'])) {
                $subregionName = $countryData['subregion'];
                $subregionCode = Str::slug($subregionName);
                // Only add if it's not already added (e.g., if region and subregion are the same string)
                if (!isset($regionsToInsert[$subregionCode])) {
                    $regionsToInsert[$subregionCode] = $subregionName;
                }
            }

            // Insert unique regions for the current country
            foreach ($regionsToInsert as $code => $name) {
                // Check if a region with the same country_id and code already exists
                // to prevent unique constraint violations on subsequent runs or if API data has duplicates
                $existingRegion = DB::table('regions')
                                    ->where('country_id', $countryId)
                                    ->where('code', $code)
                                    ->first();

                if (!$existingRegion) {
                    DB::table('regions')->insert([
                        'country_id' => $countryId,
                        'name' => $name,
                        'code' => $code,
                        'is_active' => true, // Default to true as per migration
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Database seeding completed successfully!');
    }
}
