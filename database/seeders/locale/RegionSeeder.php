<?php
// database/seeders/RegionSeeder.php

namespace Database\Seeders\locale;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class RegionSeeder extends Seeder
{
    public function run()
    {
        // Increase memory limit for this seeder
        ini_set('memory_limit', '512M'); // Or '1024M', '-1' for unlimited (use with caution!)

        $this->command->info('Starting region seeding process...');
        $path = storage_path('app/private/database/regions.sql');
        $lines = file($path);
        if (!$lines) {
            throw new \Exception('Error reading PageData.php file ' . $path);
        }
        try {
            foreach ($lines as $line) {
                DB::insert($line);
            }

            return;
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die;
        }
        // Get all countries from database
        $countries = Country::all();
        $totalCountries = count($countries);
        $processedCountries = 0;

        foreach ($countries as $country) {
            $processedCountries++;
            $this->command->info("\nProcessing {$country->name} ({$processedCountries}/{$totalCountries})");

            // Try API first
            $this->fetchRegionsFromGeoNames($country);
        }

        // It's good practice to reset the memory limit if you have subsequent seeders
        // that don't require this high limit, though it will be reset after the script finishes.
        ini_restore('memory_limit'); // Restores to the value from php.ini

        $this->command->info("\nRegion seeding completed!");
    }


    protected function fetchRegionsFromGeoNames(Country $country): void
    {
        $regions = $country->regions()->get();
        $regionCount = $regions->count();
        $username = config('services.geonames.username', 'demo'); // Register for free at geonames.org
        $size = 1000;
        $finished = false;
        $offset = $regionCount ? $regionCount + 1 : 0; // Start from existing count if any
        $total = null;
        $step = 0;
        while (!$finished) {
            if ($step > 0 && $total === null) {
                $this->command->error("Total results count is not available for {$country->name}. Cannot paginate.");
                return;
            }
            if ($total !== null && $offset >= $total) {
                $finished = true;
                break;
            }
            $this->command->info("Fetching regions for {$country->name} (offset: $offset)");
            $response = Http::get("http://api.geonames.org/searchJSON", [
                'country' => $country->iso2,
                'maxRows' => $size,
                'startRow' => $offset, // Adjust as needed for pagination
                'username' => $username,
            ]);
            if ($response->successful()) {
                $data = $response->json();
                if ($step === 0) {
                    $total = $data['totalResultsCount'] ?? null;
                }
                $geoNames = $data['geonames'] ?? [];
                $geoNamesCount = count($geoNames);
                if (!$geoNamesCount) {
                    $finished = true;
                    break;
                }
                $offset += $geoNamesCount; // Increment offset for next batch
                $this->processRegions($geoNames, $country);
            }
        }
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

        if (array_key_exists('name', $apiRegion) && str_contains($apiRegion['name'], 'All Region')) {
            return null;
        }
        if (array_key_exists('description', $apiRegion) && str_contains($apiRegion['description'], 'continent')) {
            return null;
        }
        if (array_key_exists('category', $apiRegion) && str_contains($apiRegion['category'], 'country')) {
            return null;
        }
        return [
            'admin_name' => $apiRegion['adminName1'] ?? null,
            'toponym_name' => $apiRegion['toponymName'] ?? null,
            'category' => $apiRegion['fclName'] ?? null,
            'description' => $apiRegion['fcodeName'] ?? null,
            'lng' => $apiRegion['lng'] ?? null,
            'lat' => $apiRegion['lat'] ?? null,
            'population' => $apiRegion['population'] ?? null,
            'country_id' => $country->id,
            'name' => $apiRegion['name'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
