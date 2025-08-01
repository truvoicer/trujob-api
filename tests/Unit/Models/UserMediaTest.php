<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserMediaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var UserMedia
     */
    private $userMedia;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        // Create a UserMedia instance for testing
        $this->userMedia = UserMedia::factory()->create([
            'user_id' => $user->id,
        ]);
    }

    public function test_it_has_a_user_relationship()
    {
        // Arrange
        $this->assertInstanceOf(UserMedia::class, $this->userMedia);

        // Act
        $relation = $this->userMedia->user();

        // Assert
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('user_id', $relation->getForeignKeyName());
        $this->assertEquals('id', $relation->getOwnerKeyName());
        $this->assertInstanceOf(User::class, $relation->getRelated());
    }
}
