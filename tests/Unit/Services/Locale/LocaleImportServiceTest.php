<?php

namespace Tests\Unit\Services\Locale;

use App\Models\Country;
use App\Models\Currency;
use App\Services\Locale\LocaleImportService;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocaleImportServiceTest extends TestCase
{
    /**
     * @var LocaleImportService
     */
    private $localeImportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localeImportService = new LocaleImportService();

        // Create necessary directories in storage for testing
        Storage::fake('local'); // Use a fake disk for testing

        // Create dummy JSON files
        $iso3Data = json_encode(['US' => 'USA', 'CA' => 'CAN']);
        $namesData = json_encode(['US' => 'United States', 'CA' => 'Canada']);
        $phoneData = json_encode(['US' => '1', 'CA' => '1']);
        $currencyData = json_encode(['US' => 'USD', 'CA' => 'CAD']);
        $currencyInfoData = json_encode(['USD' => ['name' => 'US Dollar', 'name_plural' => 'US dollars', 'code' => 'USD', 'symbol' => '$'], 'CAD' => ['name' => 'Canadian Dollar', 'name_plural' => 'Canadian dollars', 'code' => 'CAD', 'symbol' => '$']]);

        Storage::disk('local')->put('data/locale/iso3.json', $iso3Data);
        Storage::disk('local')->put('data/locale/names.json', $namesData);
        Storage::disk('local')->put('data/locale/phone.json', $phoneData);
        Storage::disk('local')->put('data/locale/currency.json', $currencyData);
        Storage::disk('local')->put('data/locale/currency-codes.json', $currencyInfoData);


        // Adjust paths to work with Storage::fake
        $this->iso3Path = Storage::disk('local')->path('data/locale/iso3.json');
        $this->namesPath = Storage::disk('local')->path('data/locale/names.json');
        $this->phonePath = Storage::disk('local')->path('data/locale/phone.json');
        $this->currencyPath = Storage::disk('local')->path('data/locale/currency.json');
        $this->currencyInfoPath = Storage::disk('local')->path('data/locale/currency-codes.json');

        // Mock database_path function
        $this->app->bind('path.database', function () {
            return Storage::disk('local')->path('');
        });

        // Mock file_get_contents
        $this->app->instance('path.database', Storage::disk('local')->path(''));

        // Clear any existing data
        Country::query()->delete();
        Currency::query()->delete();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up database after each test.  Consider using DatabaseTransactions trait instead.
        Country::query()->delete();
        Currency::query()->delete();

        // Remove the temporary directory and its contents.
        Storage::fake('local')->deleteDirectory('data');  // Clean up fake storage
        $this->app->forgetInstance('path.database'); // Restore original binding
    }

    public function testRunImport_success()
    {
        $this->localeImportService->runImport();

        $this->assertDatabaseHas('countries', [
            'name' => 'United States',
            'iso2' => 'US',
            'iso3' => 'USA',
            'phone_code' => '1',
        ]);

        $this->assertDatabaseHas('countries', [
            'name' => 'Canada',
            'iso2' => 'CA',
            'iso3' => 'CAN',
            'phone_code' => '1',
        ]);
    }

     public function testRunImport_exception_iso3()
     {
         Storage::disk('local')->delete('data/locale/iso3.json');
         $this->expectException(\Exception::class);
         $this->expectExceptionMessage('Error reading iso3.json file ' . Storage::disk('local')->path('data/locale/iso3.json'));
         $this->localeImportService->runImport();
     }

    public function testRunImport_exception_names()
    {
        Storage::disk('local')->delete('data/locale/names.json');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error reading names.json file');
        $this->localeImportService->runImport();
    }

    public function testRunImport_exception_phone()
    {
        Storage::disk('local')->delete('data/locale/phone.json');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error reading phone.json file');
        $this->localeImportService->runImport();
    }

    public function testRunImport_exception_currency()
    {
        Storage::disk('local')->delete('data/locale/currency.json');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error reading currency.json file');
        $this->localeImportService->runImport();
    }

    public function testRunImport_exception_currency_codes()
    {
        Storage::disk('local')->delete('data/locale/currency-codes.json');
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error reading currency-codes.json file');
        $this->localeImportService->runImport();
    }
}