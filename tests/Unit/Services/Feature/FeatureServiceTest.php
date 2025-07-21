<?php

namespace Tests\Unit\Services\Feature;

use App\Models\Feature;
use App\Services\Feature\FeatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FeatureService $featureService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->featureService = new FeatureService();
    }

    public function testCreateFeature(): void
    {
        $data = [
            'name' => 'Test Feature',
            'description' => 'Test Description',
        ];

        $result = $this->featureService->createFeature($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('features', $data);
    }

    public function testCreateFeatureThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating product feature');

        // Simulate a failure by providing invalid data that violates database constraints.
        $data = [
            'name' => str_repeat('A', 256), // Name too long
            'description' => 'Test Description',
        ];

        $this->featureService->createFeature($data);
    }

    public function testUpdateFeature(): void
    {
        $feature = Feature::factory()->create();

        $data = [
            'name' => 'Updated Feature Name',
            'description' => 'Updated Feature Description',
        ];

        $result = $this->featureService->updateFeature($feature, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('features', $data);
    }

    public function testUpdateFeatureThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating product feature');

        $feature = Feature::factory()->create();

        // Simulate a failure by providing invalid data that violates database constraints.
        $data = [
            'name' => str_repeat('A', 256), // Name too long
            'description' => 'Test Description',
        ];

        $this->featureService->updateFeature($feature, $data);
    }


    public function testDeleteFeature(): void
    {
        $feature = Feature::factory()->create();

        $result = $this->featureService->deleteFeature($feature);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('features', ['id' => $feature->id]);
    }

    public function testDeleteFeatureThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting product feature');

        // Create a mock Feature object that simulates a failed deletion.
        $feature = \Mockery::mock(Feature::class);
        $feature->shouldReceive('delete')->once()->andReturn(false);

        $this->featureService->deleteFeature($feature);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
