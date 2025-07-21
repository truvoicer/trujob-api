<?php

namespace Tests\Unit\Services\Region;

use App\Enums\MorphEntity;
use App\Http\Resources\Region\RegionResource;
use App\Models\Region;
use App\Models\ShippingZone;
use App\Models\ShippingZoneAble;
use App\Repositories\RegionRepository;
use App\Services\Region\RegionShippingZoneAbleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegionShippingZoneAbleServiceTest extends TestCase
{
    use RefreshDatabase;

    private RegionShippingZoneAbleService $regionShippingZoneAbleService;
    private RegionRepository $regionRepository;
    private Region $region;
    private ShippingZone $shippingZone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->regionRepository = $this->mock(RegionRepository::class);
        $this->regionShippingZoneAbleService = new RegionShippingZoneAbleService($this->regionRepository);
        $this->region = Region::factory()->create();
        $this->shippingZone = ShippingZone::factory()->create();
    }

    public function test_validateRequest_valid_data(): void
    {
        $requestData = ['shipping_zoneable_id' => $this->region->id];
        $request = new Request($requestData);
        app()->instance('request', $request);

        $this->assertTrue($this->regionShippingZoneAbleService->validateRequest());
    }

    public function test_validateRequest_invalid_data(): void
    {
        $requestData = ['shipping_zoneable_id' => 999];
        $request = new Request($requestData);
        app()->instance('request', $request);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        try {
            $this->regionShippingZoneAbleService->validateRequest();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('shipping_zoneable_id', $e->errors());
            throw $e;
        }
    }

    public function test_attachShippingZoneAble_success(): void
    {
        $data = ['shipping_zoneable_id' => $this->region->id];
        $this->regionRepository->shouldReceive('findById')
            ->once()
            ->with($data['shipping_zoneable_id'])
            ->andReturn($this->region);

        $this->regionShippingZoneAbleService->attachShippingZoneAble($this->shippingZone, $data);

        $this->assertDatabaseHas('shipping_zone_ables', [
            'shipping_zone_id' => $this->shippingZone->id,
            'shipping_zoneable_id' => $this->region->id,
            'shipping_zoneable_type' => MorphEntity::REGION->value,
        ]);
    }

    public function test_attachShippingZoneAble_region_not_found(): void
    {
        $data = ['shipping_zoneable_id' => 999];

        $this->regionRepository->shouldReceive('findById')
            ->once()
            ->with($data['shipping_zoneable_id'])
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Region not found');

        $this->regionShippingZoneAbleService->attachShippingZoneAble($this->shippingZone, $data);
    }

    public function test_syncShippingZoneAble_adds_new_region(): void
    {
        $data = [['shipping_zoneable_id' => $this->region->id]];

        $this->shippingZone->shippingZoneAbles()->delete();

        $this->regionRepository->shouldReceive('findById')
            ->once()
            ->with($this->region->id)
            ->andReturn($this->region);

        $this->regionShippingZoneAbleService->syncShippingZoneAble($this->shippingZone, $data);

        $this->assertDatabaseHas('shipping_zone_ables', [
            'shipping_zone_id' => $this->shippingZone->id,
            'shipping_zoneable_id' => $this->region->id,
            'shipping_zoneable_type' => MorphEntity::REGION->value,
        ]);
    }

    public function test_syncShippingZoneAble_removes_existing_region(): void
    {
        $shippingZoneAble = ShippingZoneAble::factory()->create([
            'shipping_zone_id' => $this->shippingZone->id,
            'shipping_zoneable_id' => $this->region->id,
            'shipping_zoneable_type' => MorphEntity::REGION->value,
        ]);

        $data = [];

        $this->regionShippingZoneAbleService->syncShippingZoneAble($this->shippingZone, $data);

        $this->assertDatabaseMissing('shipping_zone_ables', [
            'id' => $shippingZoneAble->id,
        ]);
    }

    public function test_detachShippingZoneAble_success(): void
    {
        $shippingZoneAble = ShippingZoneAble::factory()->create([
            'shipping_zone_id' => $this->shippingZone->id,
            'shipping_zoneable_id' => $this->region->id,
            'shipping_zoneable_type' => MorphEntity::REGION->value,
        ]);

        $data = ['shipping_zoneable_id' => $this->region->id];
        $this->regionShippingZoneAbleService->detachShippingZoneAble($this->shippingZone, $data);

        $this->assertDatabaseMissing('shipping_zone_ables', [
            'id' => $shippingZoneAble->id,
        ]);
    }

    public function test_getShippingZoneableEntityResourceData_success(): void
    {
        $shippingZoneAble = ShippingZoneAble::factory()->create([
            'shipping_zone_id' => $this->shippingZone->id,
            'shipping_zoneable_id' => $this->region->id,
            'shipping_zoneable_type' => MorphEntity::REGION->value,
        ]);

        $resource = new \Illuminate\Http\Resources\Json\JsonResource($shippingZoneAble);

        $result = $this->regionShippingZoneAbleService->getShippingZoneableEntityResourceData($resource);

        $this->assertArrayHasKey('region', $result);
        $this->assertInstanceOf(RegionResource::class, $result['region']);
    }
}