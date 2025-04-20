<?php

namespace App\Helpers;

use App\Models\Site;
use App\Models\SiteUser;

class SiteHelper
{
    public static function getCurrentSite()
    {
        $user = null;
        $site = null;
        $requestUser = request()->user();
        if ($requestUser instanceof SiteUser) {
            $user = $requestUser->user;
            $site = $requestUser->site;
        } elseif ($requestUser instanceof Site) {
            $site = $requestUser;
        } 
        return [
            $site,
            $user
        ];
    }
}