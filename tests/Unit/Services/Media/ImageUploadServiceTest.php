<?php

namespace Tests\Unit\Services\Media;

use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class ImageUploadServiceTest extends TestCase
{
    /**
     * @var ImageUploadService
     */
    private $imageUploadService;

    /**
     * @var Request
     */
    private $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new Request();
        $this->imageUploadService = new ImageUploadService($this->request);
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testRequestImageUploadSuccess(): void
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $this->request->files->set('image', $file);

        $path = 'images';
        $name = 'test_image';

        $result = $this->imageUploadService->requestImageUpload('image', $path, $name);

        $this->assertNotEmpty($result);
        Storage::disk('public')->assertExists($path . '/' . $name . '.jpg');
    }

    public function testRequestImageUploadNoFile(): void
    {
        $path = 'images';
        $name = 'test_image';

        $result = $this->imageUploadService->requestImageUpload('image', $path, $name);

        $this->assertFalse($result);
    }

    public function testImageUploadSuccess(): void
    {
        $file = UploadedFile::fake()->image('test.png');
        $path = 'images';
        $name = 'test_image';

        $result = $this->imageUploadService->imageUpload($file, $path, $name);

        $this->assertNotEmpty($result);
        Storage::disk('public')->assertExists("public/$path/" . $name . '.png');
    }

    public function testImageUploadFailure(): void
    {
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('storePubliclyAs')
            ->andReturn(false);
        $file->shouldReceive('extension')
            ->andReturn('jpg');

        $path = 'images';
        $name = 'test_image';

        $result = $this->imageUploadService->imageUpload($file, $path, $name);

        $this->assertFalse($result);
    }

    public function testGetErrors(): void
    {
        $this->assertIsArray($this->imageUploadService->getErrors());
        $this->assertEmpty($this->imageUploadService->getErrors());
    }

    public function testAddError(): void
    {
        $message = 'Test error message';
        $data = ['key' => 'value'];

        $this->imageUploadService->addError($message, $data);

        $errors = $this->imageUploadService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals($message, $errors[0]['message']);
        $this->assertEquals($data, $errors[0]['data']);

        $this->imageUploadService->addError($message);
        $errors = $this->imageUploadService->getErrors();
        $this->assertCount(2, $errors);
        $this->assertArrayNotHasKey('data', $errors[1]);

    }

    public function testSetErrors(): void
    {
        $errors = [
            ['message' => 'Error 1'],
            ['message' => 'Error 2', 'data' => ['key' => 'value']],
        ];

        $this->imageUploadService->setErrors($errors);

        $retrievedErrors = $this->imageUploadService->getErrors();
        $this->assertEquals($errors, $retrievedErrors);
    }
}
