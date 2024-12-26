<?php

namespace App\Http\Controllers\Api\Firebase;

use App\Http\Controllers\Controller;
use App\Models\FirebaseDevice;
use App\Services\Firebase\FirebaseAdminService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FirebaseDeviceController extends Controller
{

    private FirebaseAdminService $firebaseAdminService;

    public function registerFirebaseDevice(Request $request) {
        $this->firebaseAdminService->setUser($request->user());
        $create = $this->firebaseAdminService->registerFirebaseDevice($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error registering firebaseDevice',
                [],
                $this->firebaseAdminService->getErrorService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('FirebaseDevice registered', [], $this->firebaseAdminService->getErrorService()->getErrors());
    }

    public function createFirebaseDevice(Request $request) {
        $this->firebaseAdminService->setUser($request->user());
        $create = $this->firebaseAdminService->createFirebaseDevice($request->all());
        if (!$create) {
            return $this->sendErrorResponse(
                'Error creating firebaseDevice',
                [],
                $this->firebaseAdminService->getErrorService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('FirebaseDevice created', [], $this->firebaseAdminService->getErrorService()->getErrors());
    }

    public function updateFirebaseDevice(FirebaseDevice $firebaseDevice, Request $request) {
        $this->firebaseAdminService->setUser($request->user());
        $this->firebaseAdminService->setFirebaseDevice($firebaseDevice);
        $update = $this->firebaseAdminService->updateFirebaseDevice($request->all());
        if (!$update) {
            return $this->sendErrorResponse(
                'Error updating firebaseDevice',
                [],
                $this->firebaseAdminService->getErrorService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('FirebaseDevice updated', [], $this->firebaseAdminService->getErrorService()->getErrors());
    }
    public function deleteFirebaseDevice(FirebaseDevice $firebaseDevice, Request $request) {
        $this->firebaseAdminService->setUser($request->user());
        $this->firebaseAdminService->setFirebaseDevice($firebaseDevice);
        if (!$this->firebaseAdminService->deleteFirebaseDevice()) {
            return $this->sendErrorResponse(
                'Error deleting firebaseDevice',
                [],
                $this->firebaseAdminService->getErrorService()->getErrors(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        return $this->sendSuccessResponse('FirebaseDevice deleted', [], $this->firebaseAdminService->getErrorService()->getErrors());
    }
}
