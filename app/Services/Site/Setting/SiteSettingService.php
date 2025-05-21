<?php

namespace App\Services\Site\Setting;

use App\Models\Site;
use App\Models\SiteSetting;
use App\Services\BaseService;

class SiteSettingService extends BaseService
{
    public function createSiteSetting(array $data) {
        $siteSetting = new SiteSetting($data);
        $save = $this->site->settings()->save($siteSetting);
        if (!$save->exists()) {
            throw new \Exception('Error creating Site setting');
        }
        return true;
    }
    public function updateSiteSetting(Site $site, array $data) {
        $siteSetting = $site->settings()->first();
        if (!$siteSetting) {
            return $this->createSiteSetting($data);
        }
        if (!$siteSetting->update($data)) {
            throw new \Exception('Error updating Site setting');
        }
        return true;
    }

    public function deleteSiteSetting(SiteSetting $siteSetting) {
        if (!$siteSetting->delete()) {
            throw new \Exception('Error deleting Site setting');
        }
        return true;
    }

}
