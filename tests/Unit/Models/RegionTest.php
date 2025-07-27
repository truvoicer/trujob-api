<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Region;
use App\Models\ShippingRestriction;
use App\Models\TaxRateAble;
use App\Models\ShippingZoneAble;
use App\Models\Discountable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Region
     */
    private $region;

    protected function setUp(): void
    {
        parent::setUp();
        $country = Country::factory()->create();

        $this->region = Region::factory()->create([
            'country_id' => $country->id,
            'is_active' => true,
        ]);
    }

    public function testCountryRelationship(): void
    {
        $this->assertInstanceOf(BelongsTo::class, $this->region->country());
        $this->assertInstanceOf(Country::class, $this->region->country()->getModel());
    }

    public function testScopeActive(): void
    {
        $country = Country::factory()->create();
        // Create an inactive region
        Region::factory()->create([
            'is_active' => false,
            'country_id' => $country->id
        ]);

        // Assert that only the active region is returned
        $activeRegions = Region::where('is_active', true)->get();
        $this->assertCount(1, $activeRegions);
        $this->assertEquals($this->region->id, $activeRegions->first()->id);
    }

    public function testScopeForCountry(): void
    {
        $country = Country::factory()->create();
        $regionForCountry = Region::factory()->create(['country_id' => $country->id]);

        $regions = Region::forCountry($country->id)->get();

        $this->assertCount(1, $regions);
        $this->assertEquals($regionForCountry->id, $regions->first()->id);
    }

    public function testShippingRestrictionsRelationship(): void
    {
        $this->assertInstanceOf(MorphMany::class, $this->region->shippingRestrictions());
        $this->assertInstanceOf(ShippingRestriction::class, $this->region->shippingRestrictions()->getModel());
    }

    public function testTaxRateAblesRelationship(): void
    {
        $this->assertInstanceOf(MorphMany::class, $this->region->taxRateAbles());
        $this->assertInstanceOf(TaxRateAble::class, $this->region->taxRateAbles()->getModel());
    }

    public function testShippingZoneAblesRelationship(): void
    {
        $this->assertInstanceOf(MorphMany::class, $this->region->shippingZoneAbles());
        $this->assertInstanceOf(ShippingZoneAble::class, $this->region->shippingZoneAbles()->getModel());
    }

    public function testDiscountablesRelationship(): void
    {
        $this->assertInstanceOf(MorphMany::class, $this->region->discountables());
        $this->assertInstanceOf(Discountable::class, $this->region->discountables()->getModel());
    }
}
