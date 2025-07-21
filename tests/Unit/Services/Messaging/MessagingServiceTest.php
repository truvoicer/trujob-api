<?php

namespace Tests\Unit\Services\Messaging;

use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\Product;
use App\Models\User;
use App\Services\Messaging\MessagingService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingServiceTest extends TestCase
{
    use RefreshDatabase;

    private MessagingService $messagingService;
    private User $user;
    private Product $product;
    private MessagingGroup $messagingGroup;
    private MessagingGroupMessage $messagingGroupMessage;
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->product = Product::factory()->create();
        $this->messagingGroup = MessagingGroup::factory()->create();
        $this->messagingGroupMessage = MessagingGroupMessage::factory()->create();
        $this->request = new Request();
        $this->messagingService = new MessagingService($this->request);
    }

    public function testCreateMessageGroup(): void
    {
        $this->messagingService->setUser($this->user);
        $this->messagingService->setProduct($this->product);

        $data = ['message' => 'Test Message'];

        $result = $this->messagingService->createMessageGroup($data);

        $this->assertTrue($result);
        $this->assertEmpty($this->messagingService->getErrors());
    }

    public function testCreateMessageGroupMessage(): void
    {
        $this->messagingService->setMessagingGroup($this->messagingGroup);

        $data = ['message' => 'Test Message'];

        $result = $this->messagingService->createMessageGroupMessage($data);

        $this->assertTrue($result);
        $this->assertEmpty($this->messagingService->getErrors());
    }

    public function testUpdateMessage(): void
    {
        $this->messagingService->setMessagingGroupMessage($this->messagingGroupMessage);

        $data = ['message' => 'Updated Message'];

        $result = $this->messagingService->updateMessage($data);

        $this->assertTrue($result);
        $this->assertEmpty($this->messagingService->getErrors());
    }

    public function testDeleteMessage(): void
    {
        $this->messagingService->setMessagingGroupMessage($this->messagingGroupMessage);

        $result = $this->messagingService->deleteMessage();

        $this->assertTrue($result);
        $this->assertEmpty($this->messagingService->getErrors());
    }

    public function testDeleteMessageGroup(): void
    {
        $this->messagingService->setMessagingGroup($this->messagingGroup);

        $result = $this->messagingService->deleteMessageGroup();

        $this->assertTrue($result);
        $this->assertEmpty($this->messagingService->getErrors());
    }

    public function testGetErrors(): void
    {
        $this->assertIsArray($this->messagingService->getErrors());
    }

    public function testAddError(): void
    {
        $this->messagingService->addError('Test Error', ['data' => 'test']);
        $errors = $this->messagingService->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Test Error', $errors[0]['message']);
        $this->assertEquals(['data' => 'test'], $errors[0]['data']);

        $this->messagingService->addError('Test Error');
        $errors = $this->messagingService->getErrors();
        $this->assertCount(2, $errors);
        $this->assertEquals('Test Error', $errors[1]['message']);
    }

    public function testSetErrors(): void
    {
        $errors = [['message' => 'Error 1'], ['message' => 'Error 2']];
        $this->messagingService->setErrors($errors);
        $this->assertEquals($errors, $this->messagingService->getErrors());
    }

    public function testSetUser(): void
    {
        $this->messagingService->setUser($this->user);
        $this->assertObjectHasAttribute('user', $this->messagingService);
    }

    public function testGetProduct(): void
    {
        $this->messagingService->setProduct($this->product);
        $this->assertInstanceOf(Product::class, $this->messagingService->getProduct());
    }

    public function testSetProduct(): void
    {
        $this->messagingService->setProduct($this->product);
        $this->assertObjectHasAttribute('product', $this->messagingService);
    }

    public function testGetMessagingGroup(): void
    {
        $this->messagingService->setMessagingGroup($this->messagingGroup);
        $this->assertInstanceOf(MessagingGroup::class, $this->messagingService->getMessagingGroup());
    }

    public function testSetMessagingGroup(): void
    {
        $this->messagingService->setMessagingGroup($this->messagingGroup);
        $this->assertObjectHasAttribute('messagingGroup', $this->messagingService);
    }

    public function testGetMessagingGroupMessage(): void
    {
        $this->messagingService->setMessagingGroupMessage($this->messagingGroupMessage);
        $this->assertInstanceOf(MessagingGroupMessage::class, $this->messagingService->getMessagingGroupMessage());
    }

    public function testSetMessagingGroupMessage(): void
    {
        $this->messagingService->setMessagingGroupMessage($this->messagingGroupMessage);
        $this->assertObjectHasAttribute('messagingGroupMessage', $this->messagingService);
    }
}
