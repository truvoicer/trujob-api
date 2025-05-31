<?php

namespace App\Services\Firebase;

use App\Models\Country;
use App\Models\FirebaseDevice;
use App\Models\FirebaseTopic;
use App\Models\FirebaseTopicDevice;
use App\Models\User;
use App\Services\BaseService;
use App\Services\Media\ImageUploadService;
use App\Services\ResultsService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\Topic;

class FirebaseMessagingService extends BaseService
{
    const IMAGE_STORE_PATH = 'firebase/images';
    protected FirebaseDevice $firebaseDevice;
    protected FirebaseTopic $firebaseTopic;
    protected FirebaseTopicDevice $firebaseTopicDevice;

    protected ImageUploadService $imageUploadService;

    protected Messaging $messaging;

    private ResultsService $resultsService;

    private string $title;
    private string $body;

    private ?bool $hasImage = false;
    private ?string $imageKey;
    private ?string $imageUrl;


    public function __construct(Messaging $messaging, ResultsService $errorService)
    {
        $this->resultsService = $errorService;
        $this->messaging = $messaging;
    }

    private function buildMessageData()
    {
        if (!isset($this->title)) {
            $this->resultsService->addError('title must be set');
            return false;
        }
        $messageData = [
            'title' => $this->title,
            'body' => $this->body
        ];
        if ($this->hasImage) {
            $uploadImage = $this->handleUpload();
            if ($uploadImage || is_string($uploadImage)) {
                $this->imageUrl = $messageData['image'] = $uploadImage;
            }
        }
        return $messageData;
    }

    private function fetchTopics(?bool $allTopics = false, array $topicIds = []) {
        if ($allTopics) {
            $topics = FirebaseTopic::select('name')->whereIn('id', $topicIds)->all();
        } else {
            $topics = FirebaseTopic::select('name')->all();
        }
        return array_map(function (FirebaseTopic $firebaseTopic) {
            return $firebaseTopic->name;
        }, $topics);
    }


    private function fetchDevices(?bool $allDevices = false, array $deviceIds = []) {
        if ($allDevices) {
            $devices = FirebaseDevice::select('register_token')->whereIn('id', $deviceIds)->all();
        } else {
            $devices = FirebaseDevice::select('register_token')->all();
        }
        return array_map(function (FirebaseDevice $firebaseDevice) {
            return $firebaseDevice->register_token;
        }, $devices);
    }

    public function sendMessageToTopic(?bool $allTopics = false, array $topicIds = [])
    {
        if ($allTopics) {
            $getTopicNames = $this->fetchTopics(true);

        } else {
            $getTopicNames = $this->fetchTopics(false, $topicIds);
        }

        if (!count($getTopicNames)) {
            $this->resultsService->addError('No topics selected/available');
            return false;
        }
        $buildData = $this->buildMessageData('tr_news_app_topic_select');
        $results = [];
        foreach ($getTopicNames as $topicName) {
            $message = CloudMessage
                ::withTarget('topic', trim($topicName))
                ->withNotification($this->buildNotification($buildData));
            if (!array_key_exists($topicName, $results)) {
                $results[$topicName] = [];
            }
            $results[$topicName][] = $this->messaging->send($message);
        }
        $this->resultsService->setResults($results);
        return true;
    }

    public function sendMessageToDevice(?bool $allDevices = false, array $deviceIds = [])
    {
        if ($allDevices) {
            $getDeviceTokens = $this->fetchDevices(true);

        } else {
            $getDeviceTokens = $this->fetchDevices(false, $deviceIds);
        }

        if (!count($getDeviceTokens)) {
            $this->resultsService->addError('No devices selected/available');
            return false;
        }
        $buildData = $this->buildMessageData();
        $message = CloudMessage::new()
            ->withNotification($this->buildNotification($buildData));
        $results = $this->multicastMessageResponseHandler(
            $this->messaging->sendMulticast($message, $getDeviceTokens)
        );
        $this->resultsService->setResults($results);
        return true;
    }

    private function buildNotification()
    {
        $notification = Notification::create($this->title, $this->body);
        $notification = $notification->withTitle($this->title);
        $notification = $notification->withBody($this->body);
        if (isset($this->imageUrl) && is_string($this->imageUrl)) {
            $notification = $notification->withImageUrl($this->imageUrl);
        }
        return $notification;
    }

    private function multicastMessageResponseHandler(MulticastSendReport $report): array
    {
        $response = [
            "successes" => [
                "count" => $report->successes()->count()
            ],
            "failures" => [
                "count" => $report->failures()->count(),
            ],
            "validTokens" => $report->validTokens(),
            "unknownTokens" => $report->unknownTokens(),
            "invalidTokens" => $report->invalidTokens(),
        ];

        if ($report->hasFailures()) {
            $response["failures"]["messages"] = [];
            foreach ($report->failures()->getItems() as $failure) {
                $response["failures"]["messages"][] = $failure->error()->getMessage();
            }
        }
        return $response;
    }

    public function handleUpload() {
        try {
            $name = 'fb_image_' . Carbon::now()->timestamp;
            $storeImage = $this->imageUploadService->requestImageUpload($this->imageKey, self::IMAGE_STORE_PATH, $name);
            if (!$storeImage) {
                $this->resultsService->addError('Error uploading product media');
                return false;
            }

            return $storeImage;
        } catch (\Exception $exception) {
            $this->resultsService->addError($exception->getMessage());
            return false;
        }
    }
    /**
     * @return ResultsService
     */
    public function getResultsService(): ResultsService
    {
        return $this->resultsService;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @param bool|null $hasImage
     */
    public function setHasImage(?bool $hasImage): void
    {
        $this->hasImage = $hasImage;
    }

    /**
     * @param string|null $imageKey
     */
    public function setImageKey(?string $imageKey): void
    {
        $this->imageKey = $imageKey;
    }

}
