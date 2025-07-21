<?php

namespace Tests\Unit\Models;

use App\Models\SiteSetting;
use App\Models\Site;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    protected $siteSetting;

    protected function setUp(): void
    {
        parent::setUp();

        // Create related models
        $site = Site::factory()->create();
        $country = Country::factory()->create();
        $currency = Currency::factory()->create();
        $language = Language::factory()->create();

        // Create a SiteSetting instance for testing
        $this->siteSetting = SiteSetting::factory()->create([
            'site_id' => $site->id,
            'country_id' => $country->id,
            'currency_id' => $currency->id,
            'language_id' => $language->id,
        ]);
    }

    public function testSiteRelationship()
    {
        $this->assertInstanceOf(Site::class, $this->siteSetting->site);
    }

    public function testCountryRelationship()
    {
        $this->assertInstanceOf(Country::class, $this->siteSetting->country);
    }

    public function testCurrencyRelationship()
    {
        $this->assertInstanceOf(Currency::class, $this->siteSetting->currency);
    }

    public function testLanguageRelationship()
    {
        $this->assertInstanceOf(Language::class, $this->siteSetting->language);
    }
}