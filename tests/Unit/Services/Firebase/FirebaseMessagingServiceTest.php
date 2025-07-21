<?php

namespace Tests\Unit\Services\Firebase;

use App\Models\FirebaseDevice;
use App\Models\FirebaseTopic;
use App\Services\Firebase\FirebaseMessagingService;
use App\Services\Media\ImageUploadService;
use App\Services\ResultsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class FirebaseMessagingServiceTest extends TestCase
{
    use RefreshDatabase;

    private MockObject|Messaging $messagingMock;
    private MockObject|ResultsService $resultsServiceMock;
    private FirebaseMessagingService $firebaseMessagingService;
    private MockObject|ImageUploadService $imageUploadServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->messagingMock = $this->createMock(Messaging::class);
        $this->resultsServiceMock = $this->createMock(ResultsService::class);
        $this->imageUploadServiceMock = $this->createMock(ImageUploadService::class);

        $this->firebaseMessagingService = new FirebaseMessagingService(
            $this->messagingMock,
            $this->resultsServiceMock,
        );

        // Inject the mock image upload service
        $reflection = new \ReflectionClass($this->firebaseMessagingService);
        $reflectionProperty = $reflection->getProperty('imageUploadService');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->firebaseMessagingService, $this->imageUploadServiceMock);
    }

    public function testSendMessageToTopicNoTopics(): void
    {
        $this->resultsServiceMock->expects($this->once())
            ->method('addError')
            ->with('No topics selected/available');

        $result = $this->firebaseMessagingService->sendMessageToTopic(false, []);

        $this->assertFalse($result);
    }

    public function testSendMessageToTopicSuccess(): void
    {
        // Create a topic in the database.
        FirebaseTopic::create(['name' => 'test-topic']);

        $this->firebaseMessagingService->setTitle('Test Title');
        $this->firebaseMessagingService->setBody('Test Body');

        $this->messagingMock->expects($this->once())
            ->method('send');

        $this->resultsServiceMock->expects($this->once())
            ->method('setResults')
            ->willReturn(null);

        $result = $this->firebaseMessagingService->sendMessageToTopic(false, []);

        $this->assertTrue($result);
    }

    public function testSendMessageToDeviceNoDevices(): void
    {
        $this->resultsServiceMock->expects($this->once())
            ->method('addError')
            ->with('No devices selected/available');

        $result = $this->firebaseMessagingService->sendMessageToDevice(false, []);

        $this->assertFalse($result);
    }

    public function testSendMessageToDeviceSuccess(): void
    {
        // Create a device in the database.
        FirebaseDevice::create(['register_token' => 'test-token']);

        $this->firebaseMessagingService->setTitle('Test Title');
        $this->firebaseMessagingService->setBody('Test Body');

        $multicastReport = $this->createMock(MulticastSendReport::class);
        $multicastReport->method('successes')->willReturn(collect([]));
        $multicastReport->method('failures')->willReturn(collect([]));
        $multicastReport->method('validTokens')->willReturn([]);
        $multicastReport->method('invalidTokens')->willReturn([]);
        $multicastReport->method('unknownTokens')->willReturn([]);

        $this->messagingMock->expects($this->once())
            ->method('sendMulticast')
            ->willReturn($multicastReport);

        $this->resultsServiceMock->expects($this->once())
            ->method('setResults')
            ->willReturn(null);

        $result = $this->firebaseMessagingService->sendMessageToDevice(false, []);

        $this->assertTrue($result);
    }

    public function testHandleUploadSuccess(): void
    {
        $this->firebaseMessagingService->setImageKey('test-image-key');

        $this->imageUploadServiceMock->expects($this->once())
            ->method('requestImageUpload')
            ->willReturn('path/to/uploaded/image.jpg');

        $result = $this->firebaseMessagingService->handleUpload();

        $this->assertEquals('path/to/uploaded/image.jpg', $result);
    }

    public function testHandleUploadFailure(): void
    {
        $this->firebaseMessagingService->setImageKey('test-image-key');

        $this->imageUploadServiceMock->expects($this->once())
            ->method('requestImageUpload')
            ->willReturn(false);

        $this->resultsServiceMock->expects($this->once())
            ->method('addError')
            ->with('Error uploading product media');

        $result = $this->firebaseMessagingService->handleUpload();

        $this->assertFalse($result);
    }

    public function testGetResultsService(): void
    {
        $this->assertInstanceOf(ResultsService::class, $this->firebaseMessagingService->getResultsService());
    }

    public function testSetTitle(): void
    {
        $title = 'Test Title';
        $this->firebaseMessagingService->setTitle($title);
        $this->assertEquals($title, $this->getHiddenProperty($this->firebaseMessagingService, 'title'));
    }

    public function testSetBody(): void
    {
        $body = 'Test Body';
        $this->firebaseMessagingService->setBody($body);
        $this->assertEquals($body, $this->getHiddenProperty($this->firebaseMessagingService, 'body'));
    }

    public function testSetHasImage(): void
    {
        $hasImage = true;
        $this->firebaseMessagingService->setHasImage($hasImage);
        $this->assertTrue($this->getHiddenProperty($this->firebaseMessagingService, 'hasImage'));

        $hasImage = false;
        $this->firebaseMessagingService->setHasImage($hasImage);
        $this->assertFalse($this->getHiddenProperty($this->firebaseMessagingService, 'hasImage'));

        $hasImage = null;
        $this->firebaseMessagingService->setHasImage($hasImage);
        $this->assertNull($this->getHiddenProperty($this->firebaseMessagingService, 'hasImage'));
    }

    public function testSetImageKey(): void
    {
        $imageKey = 'test-image-key';
        $this->firebaseMessagingService->setImageKey($imageKey);
        $this->assertEquals($imageKey, $this->getHiddenProperty($this->firebaseMessagingService, 'imageKey'));
    }

    private function getHiddenProperty($object, $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}