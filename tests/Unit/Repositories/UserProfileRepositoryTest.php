<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
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


    public function test_it_can_find_user_profiles_by_params(): void
    {
        // Arrange
        $user = User::factory()->create();
        UserProfile::factory()->count(3)->create(['user_id' => $user->id]);

        $sort = 'id';
        $order = 'asc';
        $count = 2;

        // Act
        $userProfiles = $this->userProfileRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $userProfiles);
        $this->assertEquals($userProfiles->first()->id, 1);
    }


    public function test_it_can_get_the_user_profile_model(): void
    {
        // Act
        $model = $this->userProfileRepository->getModel();

        // Assert
        $this->assertInstanceOf(UserProfile::class, $model);
    }
}
