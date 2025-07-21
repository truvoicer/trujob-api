<?php

namespace Tests\Unit\Repositories;

use App\Models\UserMedia;
use App\Repositories\UserMediaRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserMediaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserMediaRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserMediaRepository();
    }

    /** @test */
    public function it_can_find_by_params()
    {
        // Arrange
        UserMedia::factory()->count(3)->create(); // Create some UserMedia records
        $sort = 'created_at';
        $order = 'desc';
        $count = 2;

        // Act
        $results = $this->repository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $results);
        $this->assertEquals($sort, $results->first()->sortable);
        $this->assertEquals($order, $results->first()->direction);
        $this->assertInstanceOf(UserMedia::class, $results->first());


        $sort = 'updated_at';
        $order = 'asc';
        $count = null;

        // Act
        $results = $this->repository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount(3, $results);
        $this->assertEquals($sort, $results->first()->sortable);
        $this->assertEquals($order, $results->first()->direction);
        $this->assertInstanceOf(UserMedia::class, $results->first());
    }

    /** @test */
    public function it_can_get_the_model()
    {
        // Act
        $model = $this->repository->getModel();

        // Assert
        $this->assertInstanceOf(UserMedia::class, $model);
    }

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }
}
