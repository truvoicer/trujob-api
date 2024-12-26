<?php

namespace App\Http\Controllers\Api\Firebase;

use App\Http\Controllers\Controller;
use App\Services\Firebase\FirebaseMessagingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FirebaseMessageController extends Controller
{

    private FirebaseMessagingService $firebaseMessagingService;

    public function sendMessageToDevice(Request $request)
    {
        $this->firebaseMessagingService->setUser($request->user());
        $sendMessages = $this->firebaseMessagingService->sendMessageToDevice(
            $request->get('all_devices'),
            $request->get('device_ids'),
        );
        if (!$sendMessages) {
            return $this->sendErrorResponse(
                'Error sending messages to device/s',
                [],
                $this->firebaseMessagingService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Messages sent',
            [],
            $this->firebaseMessagingService->getResultsService()->getResults()
        );
    }

    public function sendMessageToTopic(Request $request)
    {
        $this->firebaseMessagingService->setUser($request->user());
        $sendMessages = $this->firebaseMessagingService->sendMessageToTopic(
            $request->get('all_topics'),
            $request->get('topic_ids'),
        );
        if (!$sendMessages) {
            return $this->sendErrorResponse(
                'Error sending messages to topic/s',
                [],
                $this->firebaseMessagingService->getResultsService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse(
            'Messages sent',
            [],
            $this->firebaseMessagingService->getResultsService()->getResults()
        );
    }

}
