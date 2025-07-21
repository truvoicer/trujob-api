<?php

namespace Tests\Unit\Services\Discount;

use App\Models\Discount;
use App\Models\User;
use App\Models\UserDiscountUsage;
use App\Services\Discount\UserDiscountUsageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDiscountUsageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserDiscountUsageService $service;
    protected User $user;
    protected Discount $discount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new UserDiscountUsageService();
        $this->user = User::factory()->create();
        $this->discount = Discount::factory()->create();
    }

    public function testGetUserDiscountUsage_existingUsage_returnsUsage(): void
    {
        // Arrange
        UserDiscountUsage::factory()->create([
            'user_id' => $this->user->id,
            'discount_id' => $this->discount->id,
        ]);

        // Act
        $usage = $this->service->getUserDiscountUsage($this->user, $this->discount);

        // Assert
        $this->assertInstanceOf(UserDiscountUsage::class, $usage);
        $this->assertEquals($this->user->id, $usage->user_id);
        $this->assertEquals($this->discount->id, $usage->discount_id);
    }

    public function testGetUserDiscountUsage_noExistingUsage_returnsNull(): void
    {
        // Act
        $usage = $this->service->getUserDiscountUsage($this->user, $this->discount);

        // Assert
        $this->assertNull($usage);
    }

    public function testCreateUsageTrack_newUsage_createsAndReturnsUsage(): void
    {
        // Arrange
        $initialDiscountUsageCount = $this->discount->usage_count;

        // Act
        $this->service->createUsageTrack($this->user, $this->discount);

        // Assert
        $this->assertDatabaseHas('user_discount_usages', [
            'user_id' => $this->user->id,
            'discount_id' => $this->discount->id,
            'usage_count' => 1,
        ]);

        $this->discount->refresh();
        $this->assertEquals($initialDiscountUsageCount + 1, $this->discount->usage_count);

    }

    public function testCreateUsageTrack_existingUsage_incrementsUsage(): void
    {
        // Arrange
        $usage = UserDiscountUsage::factory()->create([
            'user_id' => $this->user->id,
            'discount_id' => $this->discount->id,
            'usage_count' => 2,
        ]);

        // Act
        $this->service->createUsageTrack($this->user, $this->discount);

        // Assert
        $usage->refresh();
        $this->assertEquals(3, $usage->usage_count);
    }

    public function testUpdateUserDiscountUsage_validData_updatesUsage(): void
    {
        // Arrange
        $usage = UserDiscountUsage::factory()->create();
        $data = ['usage_count' => 5];

        // Act
        $updatedUsage = $this->service->updateUserDiscountUsage($usage, $data);

        // Assert
        $this->assertEquals(5, $updatedUsage->usage_count);
        $this->assertDatabaseHas('user_discount_usages', [
            'id' => $usage->id,
            'usage_count' => 5,
        ]);
    }

    public function testUpdateUserDiscountUsage_invalidData_throwsException(): void
    {
        // Arrange
        $usage = UserDiscountUsage::factory()->create();
        $data = [];

        // Assert
        $this->expectException(\Exception::class);

        // Act
        $this->service->updateUserDiscountUsage($usage, $data);
    }

    public function testDeleteUserDiscountUsage_deletesUsage(): void
    {
        // Arrange
        $usage = UserDiscountUsage::factory()->create();

        // Act
        $result = $this->service->deleteUserDiscountUsage($usage);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('user_discount_usages', ['id' => $usage->id]);
    }

    public function testDeleteUserDiscountUsage_deletionFails_throwsException(): void
    {
        // Arrange
        $usage = $this->getMockBuilder(UserDiscountUsage::class)
                      ->onlyMethods(['delete'])
                      ->getMock();

        $usage->method('delete')->willReturn(false);

        // Assert
        $this->expectException(\Exception::class);

        // Act
        $this->service->deleteUserDiscountUsage($usage);
    }
}
