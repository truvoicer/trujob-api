<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Models\UserFollow;
use App\Repositories\UserFollowRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFollowRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserFollowRepository $userFollowRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFollowRepository = new UserFollowRepository();
    }

    public function testFindByParams(): void
    {
        $user = User::factory()->create();
        $followUser = User::factory()->create();
        // Arrange
        UserFollow::factory()->count(3)->create([
            'user_id' => $user->id,
            'follow_user_id' => $followUser->id,
        ]);
        $sort = 'created_at';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->userFollowRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertArrayHasKey('id', $result->first()->toArray());
    }

    public function testFindByParamsWithoutCount(): void
    {
        $user = User::factory()->create();
        $followUser = User::factory()->create();
        // Arrange
        UserFollow::factory()->count(5)->create([
            'user_id' => $user->id,
            'follow_user_id' => $followUser->id,
        ]);
        $sort = 'created_at';
        $order = 'asc';

        // Act
        $result = $this->userFollowRepository->findByParams($sort, $order);

        // Assert
        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        $this->assertArrayHasKey('id', $result->first()->toArray());
    }


    public function testGetModel(): void
    {
        // Act
        $model = $this->userFollowRepository->getModel();

        // Assert
        $this->assertInstanceOf(UserFollow::class, $model);
    }

    protected function tearDown(): void
    {
        unset($this->userFollowRepository);
        parent::tearDown();
    }
}
