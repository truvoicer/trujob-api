<?php

namespace Tests\Unit\Services\Region;

use App\Models\Region;
use App\Repositories\RegionRepository;
use App\Services\Region\RegionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class RegionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RegionService $regionService;

    protected MockInterface $regionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->regionRepository = Mockery::mock(RegionRepository::class);
        $this->app->instance(RegionRepository::class, $this->regionRepository);
        $this->regionService = $this->app->make(RegionService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateRegionBatchSuccess(): void
    {
        $data = ['countries' => ['name' => 'Test Region', 'country_id' => 1]];

        Region::shouldReceive('create')
            ->once()
            ->with($data['countries'])
            ->andReturn(true);

        $result = $this->regionService->createRegionBatch($data);

        $this->assertTrue($result);
    }

    public function testCreateRegionBatchFailure(): void
    {
        $data = ['countries' => ['name' => 'Test Region', 'country_id' => 1]];

        Region::shouldReceive('create')
            ->once()
            ->with($data['countries'])
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating region batch');

        $this->regionService->createRegionBatch($data);
    }

    public function testCreateRegionSuccess(): void
    {
        $data = ['name' => 'Test Region', 'country_id' => 1];
        $region = new Region($data);

        $region->shouldReceive('save')
            ->once()
            ->andReturn(true);

        $this->app->instance(Region::class, $region);

        $result = $this->regionService->createRegion($data);

        $this->assertTrue($result);
    }

    public function testCreateRegionFailure(): void
    {
        $data = ['name' => 'Test Region', 'country_id' => 1];
        $region = Mockery::mock(Region::class, [$data]);
        $region->makePartial();


        $region->shouldReceive('save')
            ->once()
            ->andReturn(false);
        $this->app->instance(Region::class, $region);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating region');

        $this->regionService->createRegion($data);
    }


    public function testUpdateRegionSuccess(): void
    {
        $region = Region::factory()->create();
        $data = ['name' => 'Updated Region'];

        $result = $this->regionService->updateRegion($region, $data);

        $this->assertTrue($result);
        $this->assertEquals('Updated Region', $region->fresh()->name);
    }

    public function testUpdateRegionFailure(): void
    {
        $region = Mockery::mock(Region::class);
        $data = ['name' => 'Updated Region'];

        $region->shouldReceive('update')
            ->once()
            ->with($data)
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating region');

        $this->regionService->updateRegion($region, $data);
    }

    public function testDeleteRegionSuccess(): void
    {
        $region = Region::factory()->create();

        $result = $this->regionService->deleteRegion($region);

        $this->assertTrue($result);
        $this->assertNull(Region::find($region->id));
    }

    public function testDeleteRegionFailure(): void
    {
        $region = Mockery::mock(Region::class);

        $region->shouldReceive('delete')
            ->once()
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting region');

        $this->regionService->deleteRegion($region);
    }

    public function testGetActiveRegions(): void
    {
        $regions = Region::factory()->count(2)->make()->toArray();

        $this->regionRepository->shouldReceive('getActiveRegions')
            ->once()
            ->andReturn(collect($regions));

        $result = $this->regionService->getActiveRegions();

        $this->assertSame($regions, $result);
    }

    public function testGetRegionsByCountry(): void
    {
        $countryId = 1;
        $regions = Region::factory()->count(2)->make(['country_id' => $countryId])->toArray();

        $this->regionRepository->shouldReceive('getByCountry')
            ->once()
            ->with($countryId)
            ->andReturn(collect($regions));

        $result = $this->regionService->getRegionsByCountry($countryId);

        $this->assertSame($regions, $result);
    }
}
