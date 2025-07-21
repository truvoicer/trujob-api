<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the user relationship.
     *
     * @return void
     */
    public function testUserRelationship()
    {
        // Arrange
        $user = User::factory()->create();
        $userProfile = UserProfile::factory()->create(['user_id' => $user->id]);

        // Act
        $relatedUser = $userProfile->user;

        // Assert
        $this->assertInstanceOf(User::class, $relatedUser);
        $this->assertEquals($user->id, $relatedUser->id);
    }
}