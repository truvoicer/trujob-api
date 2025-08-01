<?php

namespace Tests\Unit\Repositories;

use App\Models\Country;
use App\Models\Region;
use App\Repositories\RegionRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private RegionRepository $regionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->regionRepository = new RegionRepository();
    }

    public function testGetModelReturnsRegionModel(): void
    {
        $model = $this->regionRepository->getModel();

        $this->assertInstanceOf(Region::class, $model);
    }

    public function testFindByParamsReturnsCorrectData(): void
    {
        // Arrange
        $country = Country::factory()->create();
        Region::factory()->count(3)->create(['country_id' => $country->id]);

        // Act
        $result = $this->regionRepository->findByParams('name', 'asc');

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function testFindByQueryParamsReturnsCorrectData(): void
    {
        // Arrange
        $country = Country::factory()->create();
        Region::factory()->count(2)->create(['country_id' => $country->id]);

        // Act
        $result = $this->regionRepository->findByQuery('test query');

        // Assert
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertCount(2, $result);
    }

    public function testGetActiveRegionsReturnsOnlyActiveRegions(): void
    {
        // Arrange
        $country = Country::factory()->create();
        Region::factory()->count(2)->create(['is_active' => true, 'country_id' => $country->id]);
        Region::factory()->count(1)->create(['is_active' => false, 'country_id' => $country->id]);

        // Act
        $activeRegions = $this->regionRepository->getActiveRegions();

        // Assert
        $this->assertInstanceOf(Collection::class, $activeRegions);
        $this->assertCount(2, $activeRegions);
        foreach ($activeRegions as $region) {
            $this->assertTrue($region->is_active);
        }
    }

    public function testGetByCountryReturnsRegionsForSpecificCountry(): void
    {
        // Arrange
        $country = Country::factory()->create();
        $country2 = Country::factory()->create();
        Region::factory()->count(2)->create(['country_id' => $country->id, 'is_active' => true]);
        Region::factory()->count(1)->create(['country_id' => $country2->id, 'is_active' => true]); // Different country
        Region::factory()->count(1)->create(['country_id' => $country->id, 'is_active' => false]); // Inactive

        // Act
        $regions = $this->regionRepository->getByCountry($country->id);

        // Assert
        $this->assertInstanceOf(Collection::class, $regions);
        $this->assertCount(2, $regions);
        foreach ($regions as $region) {
            $this->assertEquals($country->id, $region->country_id);
            $this->assertTrue($region->is_active);
        }
    }
}
