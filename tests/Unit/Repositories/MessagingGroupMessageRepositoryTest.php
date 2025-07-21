<?php

namespace Tests\Unit\Repositories;

use App\Models\MessagingGroupMessage;
use App\Repositories\MessagingGroupMessageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingGroupMessageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var MessagingGroupMessageRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MessagingGroupMessageRepository();
    }

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testGetModel(): void
    {
        $model = $this->repository->getModel();
        $this->assertInstanceOf(MessagingGroupMessage::class, $model);
    }

    public function testFindByParams(): void
    {
        // Arrange
        MessagingGroupMessage::factory()->count(3)->create();
        $sort = 'created_at';
        $order = 'asc';

        // Act
        $result = $this->repository->findByParams($sort, $order);

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);

        //Cleanup
        MessagingGroupMessage::truncate();
    }

    public function testFindByParamsWithCount(): void
    {
        // Arrange
        MessagingGroupMessage::factory()->count(5)->create();
        $sort = 'created_at';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->repository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);

        //Cleanup
        MessagingGroupMessage::truncate();
    }

    public function testFindByQuery(): void
    {
        // Arrange
        MessagingGroupMessage::factory()->count(2)->create();

        // Act
        $result = $this->repository->findByQuery(null);

        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);

        //Cleanup
        MessagingGroupMessage::truncate();
    }
}
