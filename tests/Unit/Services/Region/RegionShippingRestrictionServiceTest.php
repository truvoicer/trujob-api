<?php

namespace Tests\Unit\Services\Region;

use App\Enums\MorphEntity;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Models\Region;
use App\Models\ShippingMethod;
use App\Models\ShippingRestriction;
use App\Repositories\RegionRepository;
use App\Services\Region\RegionShippingRestrictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\TestCase;

class RegionShippingRestrictionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RegionShippingRestrictionService $service;
    protected RegionRepository $regionRepository;
    protected ShippingMethod $shippingMethod;
    protected Region $region;
    protected ShippingRestriction $shippingRestriction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->regionRepository = Mockery::mock(RegionRepository::class);
        $this->service = new RegionShippingRestrictionService($this->regionRepository);

        $this->shippingMethod = ShippingMethod::factory()->create();
        $this->region = Region::factory()->create();
        $this->shippingRestriction = ShippingRestriction::factory()->create([
            'shipping_method_id' => $this->shippingMethod->id,
            'restrictionable_id' => $this->region->id,
            'restrictionable_type' => 'App\Models\Region'
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testValidateRequestSuccess(): void
    {
        $data = ['restriction_id' => $this->region->id];
        $request = new Request($data);
        app()->instance('request', $request);

        $this->assertTrue($this->service->validateRequest());
    }

    public function testValidateRequestFails(): void
    {
        $data = ['restriction_id' => 9999]; //Non-existent region id
        $request = new Request($data);
        app()->instance('request', $request);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->service->validateRequest();
    }

    public function testStoreShippingRestrictionSuccess(): void
    {
        $data = ['restriction_id' => $this->region->id];

        $this->regionRepository->shouldReceive('findById')
            ->once()
            ->with($data['restriction_id'])
            ->andReturn($this->region);

        $shippingRestriction = $this->service->storeShippingRestriction($this->shippingMethod, $data);

        $this->assertInstanceOf(ShippingRestriction::class, $shippingRestriction);
        $this->assertEquals($this->shippingMethod->id, $shippingRestriction->shipping_method_id);
        $this->assertEquals($this->region->id, $shippingRestriction->restrictionable_id);
        $this->assertEquals('App\Models\Region', $shippingRestriction->restrictionable_type);
    }

    public function testStoreShippingRestrictionThrowsExceptionWhenRegionNotFound(): void
    {
        $data = ['restriction_id' => 999];

        $this->regionRepository->shouldReceive('findById')
            ->once()
            ->with($data['restriction_id'])
            ->andReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Region not found');

        $this->service->storeShippingRestriction($this->shippingMethod, $data);
    }

    public function testUpdateShippingRestrictionSuccess(): void
    {
        $data = ['some_field' => 'some_value'];

        $updatedShippingRestriction = $this->service->updateShippingRestriction($this->shippingRestriction, $data);

        $this->assertInstanceOf(ShippingRestriction::class, $updatedShippingRestriction);
        $this->assertEquals($this->shippingRestriction->id, $updatedShippingRestriction->id);

        $this->assertDatabaseHas('shipping_restrictions', [
            'id' => $this->shippingRestriction->id,
            'some_field' => 'some_value'
        ]);
    }

    public function testUpdateShippingRestrictionThrowsExceptionOnFailure(): void
    {
        $shippingRestriction = Mockery::mock(ShippingRestriction::class);
        $shippingRestriction->shouldReceive('update')
            ->once()
            ->with(['some_field' => 'some_value'])
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating shipping restriction');

        $this->service->updateShippingRestriction($shippingRestriction, ['some_field' => 'some_value']);
    }

    public function testDeleteShippingRestrictionSuccess(): void
    {
        $result = $this->service->deleteShippingRestriction($this->shippingRestriction);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('shipping_restrictions', ['id' => $this->shippingRestriction->id]);
    }

    public function testDeleteShippingRestrictionThrowsExceptionOnFailure(): void
    {
        $shippingRestriction = Mockery::mock(ShippingRestriction::class);
        $shippingRestriction->shouldReceive('delete')
            ->once()
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting shipping restriction');

        $this->service->deleteShippingRestriction($shippingRestriction);
    }

    public function testGetRestrictionableEntityResourceData(): void
    {
        $result = $this->service->getRestrictionableEntityResourceData($this->shippingRestriction->region);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('region', $result);
    }
}