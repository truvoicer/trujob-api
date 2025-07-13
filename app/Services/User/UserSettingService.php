<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\BaseService;

class UserSettingService extends BaseService
{

    public function updateUserSetting(array $data) {
        $createUserSetting = $this->user->userSetting()->updateOrCreate([
            'user_id' => $this->user->id,
        ], $data);
        if (!$createUserSetting) {
            throw new \Exception('Error creating user setting for user');
        }
        if (!empty($data['currency_id'])) {
            $currency = $this->user->userSetting->currency()
            ->where('id', $data['currency_id'])
            ->first();
            if (!$currency) {
                throw new \Exception('Currency not found');
            }
            $country = $currency->country()
                ->first();
            if (!$country) {
                throw new \Exception('Country not found for currency');
            }
            $this->user->userSetting()->update([
                'country_id' => $country->id,
            ]);
        }
        return true;
    }

}
