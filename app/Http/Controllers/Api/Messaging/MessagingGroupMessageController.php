<?php

namespace App\Http\Controllers\Api\Messaging;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messaging\StoreMessagingGroupMessageRequest;
use App\Http\Requests\Messaging\UpdateMessagingGroupMessageRequest;
use App\Models\Product;
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\User;
use App\Services\Messaging\MessagingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagingGroupMessageController extends Controller
{
    protected MessagingService $messagingService;

    public function __construct(MessagingService $messagingService, Request $request)
    {
        $this->messagingService = $messagingService;
    }

    public function createMessage(Product $product, MessagingGroup $messagingGroup, Request $request) {
        $this->messagingService->setUser($request->user());
        $this->messagingService->setMessagingGroup($messagingGroup);
        $create = $this->messagingService->createMessageGroupMessage($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error sending message',
                [],
                $this->messagingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Message sent', [], $this->messagingService->getErrors());
    }

    public function updateMessage(Product $product, MessagingGroup $messagingGroup, MessagingGroupMessage $messagingGroupMessage,  Request $request) {
        $this->messagingService->setUser($request->user());
        $this->messagingService->setMessagingGroupMessage($messagingGroupMessage);
        $update = $this->messagingService->updateMessage($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating message',
                [],
                $this->messagingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Message updated', [], $this->messagingService->getErrors());
    }
    public function deleteMessage(Product $product, MessagingGroup $messagingGroup, MessagingGroupMessage $messagingGroupMessage, Request $request) {
        $this->messagingService->setUser($request->user());
        $this->messagingService->setMessagingGroupMessage($messagingGroupMessage);
        if (!$this->messagingService->deleteMessage()) {
            return $this->sendErrorResponse(
                'Error deleting message',
                [],
                $this->messagingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Message deleted', [], $this->messagingService->getErrors());
    }
}
