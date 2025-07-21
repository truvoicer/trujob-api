<?php

namespace Tests\Unit\Repositories;

use App\Models\UserProfile;
use App\Repositories\UserProfileRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserProfileRepository $userProfileRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userProfileRepository = new UserProfileRepository();
    }

    /** @test */
    public function it_can_find_user_profiles_by_params(): void
    {
        // Arrange
        UserProfile::factory()->count(3)->create();
        $sort = 'id';
        $order = 'asc';
        $count = 2;

        // Act
        $userProfiles = $this->userProfileRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $userProfiles);
        $this->assertEquals($userProfiles->first()->id, 1);
    }

    /** @test */
    public function it_can_get_the_user_profile_model(): void
    {
        // Act
        $model = $this->userProfileRepository->getModel();

        // Assert
        $this->assertInstanceOf(UserProfile::class, $model);
    }
}