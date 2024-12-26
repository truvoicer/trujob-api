<?php

namespace App\Services\Firebase;

use App\Models\Country;
use App\Models\FirebaseDevice;
use App\Models\FirebaseTopic;
use App\Models\FirebaseTopicDevice;
use App\Models\User;
use App\Services\BaseService;
use App\Services\ResultsService;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Contract\Messaging;

class FirebaseAdminService extends BaseService
{
    protected FirebaseDevice $firebaseDevice;
    protected FirebaseTopic $firebaseTopic;
    protected FirebaseTopicDevice $firebaseTopicDevice;
    private ResultsService $errorService;

    public function __construct(ResultsService $errorService)
    {
        $this->errorService = $errorService;
    }

    public function addFirebaseDeviceToTopic() {
        $this->firebaseTopicDevice = new FirebaseTopicDevice();
        $this->firebaseTopicDevice->firebase_topic_id = $this->firebaseTopic->id;
        $this->firebaseTopicDevice->firebase_device_id = $this->firebaseDevice->id;
        if (!$this->firebaseTopic->devices()->save($this->firebaseTopicDevice)) {
            $this->errorService->addError('Error adding firebase device');
            return false;
        }
        return true;
    }
    public function removeFirebaseDeviceFromTopic() {
        $this->firebaseTopicDevice = new FirebaseTopicDevice();
        $this->firebaseTopicDevice->firebase_topic_id = $this->firebaseTopic->id;
        $this->firebaseTopicDevice->firebase_device_id = $this->firebaseDevice->id;
        if (!$this->firebaseTopic->devices()->delete($this->firebaseTopicDevice)) {
            $this->errorService->addError('Error adding firebase device');
            return false;
        }
        return true;
    }

    public function registerFirebaseDevice(array $data) {
        $findDevice = FirebaseDevice::where('register_token', $data['register_token'])->first();
        $save = false;
        if (!$findDevice->exists) {
            $save = $this->createFirebaseDevice($data);
        } else {
            $save = $this->updateFirebaseDevice([]);
        }
        if (!$save) {
            $this->errorService->addError('Error saving device');
            return false;
        }

        $this->firebaseTopic = FirebaseTopic::where('name', FirebaseTopic::DEFAULT_TOPIC)->first();
        $findTopicDevice = FirebaseTopicDevice::where('firebase_topic_id', $this->firebaseTopic->id)
            ->where('firebase_device_id', $this->firebaseDevice->id)
            ->first();
        if (!$findTopicDevice instanceof FirebaseTopicDevice || !$findTopicDevice->exists) {
            $this->errorService->addError('Error fetching relation');
            return false;
        }
        return $this->addFirebaseDeviceToTopic();
    }

    public function createFirebaseDevice(array $data) {
        $this->firebaseDevice = new FirebaseDevice($data);

        if (!$this->firebaseDevice->save()) {
            $this->errorService->addError('Error adding firebase device', $data);
            return false;
        }
        return true;
    }

    public function updateFirebaseDevice(array $data) {
        $this->firebaseDevice->fill($data);
        if (!$this->firebaseDevice->save()) {
            $this->errorService->addError('Error updating firebase device', $data);
            return false;
        }
        return true;
    }

    public function deleteFirebaseDevice() {
        if (!$this->firebaseDevice->delete()) {
            $this->errorService->addError('Error deleting firebase device');
            return false;
        }
        return true;
    }

    public function createFirebaseTopic(array $data) {
        $this->firebaseTopic = new FirebaseTopic($data);

        if (!$this->firebaseTopic->save()) {
            $this->errorService->addError('Error adding firebase topic', $data);
            return false;
        }
        return true;
    }
    public function updateFirebaseTopic(array $data) {
        $this->firebaseTopic->fill($data);
        if (!$this->firebaseTopic->save()) {
            $this->errorService->addError('Error updating firebase topic', $data);
            return false;
        }
        return true;
    }

    public function deleteFirebaseTopic() {
        if (!$this->firebaseTopic->delete()) {
            $this->errorService->addError('Error deleting firebase topic');
            return false;
        }
        return true;
    }

    /**
     * @return FirebaseDevice
     */
    public function getFirebaseDevice(): FirebaseDevice
    {
        return $this->firebaseDevice;
    }

    /**
     * @param FirebaseDevice $firebaseDevice
     */
    public function setFirebaseDevice(FirebaseDevice $firebaseDevice): void
    {
        $this->firebaseDevice = $firebaseDevice;
    }

    /**
     * @return FirebaseTopic
     */
    public function getFirebaseTopic(): FirebaseTopic
    {
        return $this->firebaseTopic;
    }

    /**
     * @param FirebaseTopic $firebaseTopic
     */
    public function setFirebaseTopic(FirebaseTopic $firebaseTopic): void
    {
        $this->firebaseTopic = $firebaseTopic;
    }

    /**
     * @return FirebaseTopicDevice
     */
    public function getFirebaseTopicDevice(): FirebaseTopicDevice
    {
        return $this->firebaseTopicDevice;
    }

    /**
     * @param FirebaseTopicDevice $firebaseTopicDevice
     */
    public function setFirebaseTopicDevice(FirebaseTopicDevice $firebaseTopicDevice): void
    {
        $this->firebaseTopicDevice = $firebaseTopicDevice;
    }

    /**
     * @return ResultsService
     */
    public function getErrorService(): ResultsService
    {
        return $this->errorService;
    }

}
