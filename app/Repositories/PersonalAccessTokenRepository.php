<?php

namespace App\Repositories;

use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use DateTime;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\NewAccessToken;

class PersonalAccessTokenRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(PersonalAccessToken::class);
    }

    public function getModel(): PersonalAccessToken
    {
        return parent::getModel();
    }

    public function updateTokenExpiry(PersonalAccessToken $personalAccessToken, array $data) {
        $this->setModel($personalAccessToken);

        if (!empty($data['expires_at'])) {
            $data['expires_at'] = new DateTime($data['expires_at']);
        }
        if (!$this->update($data)) {
            return false;
        }
        return true;
    }
    public function getLatestAccessToken(User|Site|SiteUser $model) {
        $dateTime = new DateTime();

        $token = $model->tokens()
            ->where('expires_at', '>', $dateTime->format('Y-m-d H:i:s'))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
        if (!$token instanceof PersonalAccessToken) {
            return null;
        }
        return $token;
    }
}
