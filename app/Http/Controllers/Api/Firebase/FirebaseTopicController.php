<?php

namespace App\Http\Controllers\Api\Firebase;

use App\Http\Controllers\Controller;
use App\Models\FirebaseTopic;
use App\Services\Firebase\FirebaseAdminService;
use App\Services\Firebase\FirebaseTopicAdminService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FirebaseTopicController extends Controller
{
    private FirebaseAdminService $firebaseAdminService;
    public function createFirebaseTopic(Request $request) {
        $this->firebaseAdminService->setUser($request->user());
        $create = $this->firebaseAdminService->createFirebaseTopic($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating firebaseTopic',
                [],
                $this->firebaseAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('FirebaseTopic created', [], $this->firebaseAdminService->getErrors());
    }

    public function updateFirebaseTopic(FirebaseTopic $firebaseTopic, Request $request) {
        $this->firebaseAdminService->setUser($request->user());
        $this->firebaseAdminService->setFirebaseTopic($firebaseTopic);
        $update = $this->firebaseAdminService->updateFirebaseTopic($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating firebaseTopic',
                [],
                $this->firebaseAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('FirebaseTopic updated', [], $this->firebaseAdminService->getErrors());
    }
    public function deleteFirebaseTopic(FirebaseTopic $firebaseTopic, Request $request) {
        $this->firebaseAdminService->setUser($request->user());
        $this->firebaseAdminService->setFirebaseTopic($firebaseTopic);
        if (!$this->firebaseAdminService->deleteFirebaseTopic()) {
            return $this->sendErrorResponse(
                'Error deleting firebaseTopic',
                [],
                $this->firebaseAdminService->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('FirebaseTopic deleted', [], $this->firebaseAdminService->getErrors());
    }
}
