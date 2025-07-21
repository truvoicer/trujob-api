<?php

namespace Tests\Unit\Repositories;

use App\Models\Feature;
use App\Repositories\FeatureRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private FeatureRepository $featureRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->featureRepository = new FeatureRepository();
    }

    /** @test */
    public function it_can_get_model()
    {
        $model = $this->featureRepository->getModel();

        $this->assertInstanceOf(Feature::class, $model);
    }

    /** @test */
    public function it_can_find_by_params()
    {
        Feature::factory()->count(3)->create();

        $result = $this->featureRepository->findByParams('name', 'asc');

        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    /** @test */
    public function it_can_find_by_query()
    {
        Feature::factory()->count(2)->create();

        $result = $this->featureRepository->findByQuery('some_query'); //The query is not actually used in the method so providing a value for it is only for completeness.

        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
}