<?php

namespace Tests\Unit\Services\User;

use App\Models\User;
use App\Models\UserProfile;
use App\Services\User\UserProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected UserProfileService $userProfileService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user); // Simulate authentication
        $this->userProfileService = app()->make(UserProfileService::class);
        $this->userProfileService->setUser($this->user);
    }

    public function testUpdateUserProfileCreatesProfileIfNoneExists()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
        ];

        $result = $this->userProfileService->updateUserProfile($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '1990-01-01 00:00:00',
        ]);
    }

    public function testUpdateUserProfileUpdatesExistingProfile()
    {
        UserProfile::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'first_name' => 'Updated John',
            'last_name' => 'Updated Doe',
            'dob' => '1991-02-02',
        ];

        $result = $this->userProfileService->updateUserProfile($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->user->id,
            'first_name' => 'Updated John',
            'last_name' => 'Updated Doe',
            'dob' => '1991-02-02 00:00:00',
        ]);
    }

     public function testUpdateUserProfileHandlesDateParsing()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '2023-10-27',
        ];

        $result = $this->userProfileService->updateUserProfile($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $this->user->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '2023-10-27 00:00:00',
        ]);
    }

    public function testUpdateUserProfileThrowsExceptionOnFailure()
    {
        $this->userProfileService->setUser(null); // Force failure

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating user profile');

        $this->userProfileService->updateUserProfile($data);
    }

}