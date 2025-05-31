<?php

namespace App\Http\Controllers\Api\Messaging;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messaging\StoreMessagingGroupRequest;
use App\Http\Requests\Messaging\UpdateMessagingGroupRequest;
use App\Models\Product;
use App\Models\MessagingGroup;
use App\Models\MessagingGroupMessage;
use App\Models\User;
use App\Services\Messaging\MessagingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagingGroupController extends Controller
{
    protected MessagingService $messagingService;

    public function __construct(MessagingService $messagingService, Request $request)
    {
        $this->messagingService = $messagingService;
    }

    public function createMessageGroup(Product $product, Request $request) {
        $this->messagingService->setUser($request->user());
        $this->messagingService->setProduct($product);
        $create = $this->messagingService->createMessageGroup($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating message group',
                [],
                $this->messagingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Message group created', [], $this->messagingService->getErrors());
    }

    public function deleteMessageGroup(MessagingGroup $messagingGroup) {
        $this->messagingService->setMessagingGroup($messagingGroup);
        if (!$this->messagingService->deleteMessageGroup()) {
            return $this->sendErrorResponse(
                'Error deleting conversation',
                [],
                $this->messagingService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('Conversation deleted', [], $this->messagingService->getErrors());
    }
}
