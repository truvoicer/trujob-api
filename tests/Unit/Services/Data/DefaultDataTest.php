<?php

namespace Tests\Unit\Services\Data;

use App\Services\Data\DefaultData;
use Tests\TestCase;

class DefaultDataTest extends TestCase
{
    /**
     * @var DefaultData
     */
    private $defaultData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->defaultData = new DefaultData();
    }

    protected function tearDown(): void
    {
        unset($this->defaultData);

        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_constant_test_user_data_exists()
    {
        $this->assertIsArray(DefaultData::TEST_USER_DATA);
    }

    public function test_constant_test_user_data_contains_expected_keys()
    {
        $expectedKeys = ['username', 'first_name', 'last_name', 'email', 'password'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, DefaultData::TEST_USER_DATA);
        }
    }

    public function test_constant_test_user_data_has_expected_values()
    {
        $this->assertEquals('testuser', DefaultData::TEST_USER_DATA['username']);
        $this->assertEquals('Test User', DefaultData::TEST_USER_DATA['first_name']);
        $this->assertEquals('Last User', DefaultData::TEST_USER_DATA['last_name']);
        $this->assertEquals('test@user.com', DefaultData::TEST_USER_DATA['email']);
        $this->assertEquals('password', DefaultData::TEST_USER_DATA['password']);
    }
}