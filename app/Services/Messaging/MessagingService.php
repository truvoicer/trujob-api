<?php

namespace App\Services\Messaging;

use App\Models\Product;
use App\Models\ProductFeature;
use App\Models\MediaProduct;
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\User;
use App\Services\Media\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessagingService
{

    private User $user;
    private Request $request;

    private Product $product;
    private MessagingGroup $messagingGroup;
    private MessagingGroupMessage $messagingGroupMessage;

    private array $errors = [];


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function createMessageGroup(array $data) {
        $this->messagingGroup = new MessagingGroup([
            'user_id' => $this->user->id
        ]);
        $createMessageGroup = $this->product->productMessagingGroup()->save($this->messagingGroup);
        if (!$createMessageGroup) {
            $this->addError('Error creating message group for user', $data);
            return false;
        }
        if (isset($data['message'])) {
            return $this->createMessageGroupMessage($data);
        }
        return true;
    }

    public function createMessageGroupMessage(array $data) {
        $this->messagingGroupMessage = new MessagingGroupMessage($data);
        $createMessage = $this->messagingGroup->messagingGroupMessage()->save($this->messagingGroupMessage);
        if (!$createMessage) {
            $this->addError('Error creating message for user', $data);
            return false;
        }
        return true;
    }

    public function updateMessage(array $data) {
        $this->messagingGroupMessage->fill($data);
        $save = $this->messagingGroupMessage->save();
        if (!$save) {
            $this->addError('Error updating message', $data);
            return false;
        }
        return true;
    }

    public function deleteMessage() {
        if (!$this->messagingGroupMessage->delete()) {
            $this->addError('Error deleting message');
            return false;
        }
        return true;
    }

    public function deleteMessageGroup() {
        if (!$this->messagingGroup->delete()) {
            $this->addError('Error deleting messages');
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $error
     */
    public function addError(string $message, ?array $data = []): void
    {
        $error = [
            'message' => $message
        ];
        if (count($data)) {
            $error['data'] = $data;
        }
        $this->errors[] = $error;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return MessagingGroup
     */
    public function getMessagingGroup(): MessagingGroup
    {
        return $this->messagingGroup;
    }

    /**
     * @param MessagingGroup $messagingGroup
     */
    public function setMessagingGroup(MessagingGroup $messagingGroup): void
    {
        $this->messagingGroup = $messagingGroup;
    }

    /**
     * @return MessagingGroupMessage
     */
    public function getMessagingGroupMessage(): MessagingGroupMessage
    {
        return $this->messagingGroupMessage;
    }

    /**
     * @param MessagingGroupMessage $messagingGroupMessage
     */
    public function setMessagingGroupMessage(MessagingGroupMessage $messagingGroupMessage): void
    {
        $this->messagingGroupMessage = $messagingGroupMessage;
    }

}
