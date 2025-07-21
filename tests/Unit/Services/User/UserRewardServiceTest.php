<?php

namespace Tests\Unit\Services\User;

use App\Models\User;
use App\Models\UserReward;
use App\Services\User\UserRewardService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class UserRewardServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserRewardService $userRewardService;
    private User $user;
    private UserReward $userReward;
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->userReward = UserReward::factory()->make(); //make, not create, we don't want it persisted until later
        $this->request = new Request();

        $this->userRewardService = new UserRewardService($this->request);
        $this->userRewardService->setUser($this->user);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_createUserReward_success(): void
    {
        $data = $this->userReward->toArray();
        $result = $this->userRewardService->createUserReward($data);

        $this->assertTrue($result);
        $this->assertEmpty($this->userRewardService->getErrors());
        $this->assertDatabaseHas('user_rewards', $data);
    }

    public function test_createUserReward_failure(): void
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('userReward->save')
            ->once()
            ->andReturn(false);

        $this->userRewardService->setUser($mockUser);

        $data = $this->userReward->toArray();
        $result = $this->userRewardService->createUserReward($data);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->userRewardService->getErrors());
        $this->assertDatabaseMissing('user_rewards', $data);
    }

    public function test_updateUserReward_success(): void
    {
        $userReward = UserReward::factory()->create();
        $this->userRewardService->setUserReward($userReward);

        $data = ['name' => 'Updated Reward Name'];

        $result = $this->userRewardService->updateUserReward($data);

        $this->assertTrue($result);
        $this->assertEmpty($this->userRewardService->getErrors());
        $this->assertDatabaseHas('user_rewards', ['id' => $userReward->id, 'name' => 'Updated Reward Name']);
    }

    public function test_updateUserReward_failure(): void
    {
        $userReward = Mockery::mock(UserReward::class);
        $userReward->shouldReceive('fill')->once();
        $userReward->shouldReceive('save')->once()->andReturn(false);

        $this->userRewardService->setUserReward($userReward);

        $data = ['name' => 'Updated Reward Name'];
        $result = $this->userRewardService->updateUserReward($data);

        $this->assertFalse($result);
        $this->assertNotEmpty($this->userRewardService->getErrors());
    }

    public function test_deleteUserReward_success(): void
    {
        $userReward = UserReward::factory()->create();
        $this->userRewardService->setUserReward($userReward);

        $result = $this->userRewardService->deleteUserReward();

        $this->assertTrue($result);
        $this->assertEmpty($this->userRewardService->getErrors());
        $this->assertDatabaseMissing('user_rewards', ['id' => $userReward->id]);
    }

    public function test_deleteUserReward_failure(): void
    {
        $userReward = Mockery::mock(UserReward::class);
        $userReward->shouldReceive('delete')->once()->andReturn(false);

        $this->userRewardService->setUserReward($userReward);

        $result = $this->userRewardService->deleteUserReward();

        $this->assertFalse($result);
        $this->assertNotEmpty($this->userRewardService->getErrors());
    }

    public function test_getErrors(): void
    {
        $this->assertIsArray($this->userRewardService->getErrors());
    }

    public function test_addError(): void
    {
        $this->userRewardService->addError('Test Error', ['key' => 'value']);
        $errors = $this->userRewardService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Test Error', $errors[0]['message']);
        $this->assertEquals(['key' => 'value'], $errors[0]['data']);

        $this->userRewardService->addError('Test Error');
        $errors = $this->userRewardService->getErrors();
        $this->assertCount(2, $errors);
    }

    public function test_setErrors(): void
    {
        $errors = [['message' => 'Error 1'], ['message' => 'Error 2']];
        $this->userRewardService->setErrors($errors);
        $this->assertEquals($errors, $this->userRewardService->getErrors());
    }

    public function test_setUser(): void
    {
        $newUser = User::factory()->create();
        $this->userRewardService->setUser($newUser);

        $this->setProperty($this->userRewardService, 'user', $newUser);
        $this->assertEquals($newUser, $this->getProperty($this->userRewardService, 'user'));
    }

    public function test_setUserReward(): void
    {
        $newUserReward = UserReward::factory()->make();
        $this->userRewardService->setUserReward($newUserReward);

        $this->setProperty($this->userRewardService, 'userReward', $newUserReward);
        $this->assertEquals($newUserReward, $this->getProperty($this->userRewardService, 'userReward'));
    }

    public function test_getUserReward(): void
    {
        $userReward = UserReward::factory()->make();
        $this->userRewardService->setUserReward($userReward);
        $this->assertEquals($userReward, $this->userRewardService->getUserReward());
    }

    /**
     * Helper method to get the value of a protected or private property.
     *
     * @param object $object
     * @param string $propertyName
     * @return mixed
     */
    private function getProperty(object $object, string $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * Helper method to set the value of a protected or private property.
     *
     * @param object $object
     * @param string $propertyName
     * @param mixed $value
     * @return void
     */
    private function setProperty(object $object, string $propertyName, $value): void
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
