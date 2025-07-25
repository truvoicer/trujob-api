<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserReview;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var UserReview
     */
    private $userReview;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        // Create a UserReview instance for testing
        $this->userReview = UserReview::factory()->create([
            'user_id' => $user->id,
        ]);
    }

    protected function tearDown(): void
    {
        unset($this->userReview);

        parent::tearDown();
    }

    /**
     * Test the user relationship.
     *
     * @return void
     */
    public function testUserRelationship()
    {
        // Assert that the user method exists and returns a BelongsTo relationship
        $this->assertInstanceOf(BelongsTo::class, $this->userReview->user());

        // Create a user to associate with the review.
        $user = User::factory()->create();
        $this->userReview->user_id = $user->id;
        $this->userReview->save();


        // Reload the review and check that the user relationship is working
        $loadedReview = UserReview::find($this->userReview->id);

        $this->assertInstanceOf(User::class, $loadedReview->user);
        $this->assertEquals($user->id, $loadedReview->user->id);
    }
}
