<?php

namespace Tests\Unit\Services\Media;

use App\Services\Media\MediaService;
use Tests\TestCase;

class MediaServiceTest extends TestCase
{
    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mediaService = new MediaService();
    }

    /**
     * Clean up the testing environment before the next test.
     */
    protected function tearDown(): void
    {
        unset($this->mediaService);

        parent::tearDown();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_class_can_be_instantiated()
    {
        $this->assertInstanceOf(MediaService::class, $this->mediaService);
    }

    /**
     * Test that the class extends BaseService.
     *
     * @return void
     */
    public function test_class_extends_base_service()
    {
        $this->assertInstanceOf(\App\Services\BaseService::class, $this->mediaService);
    }
}
