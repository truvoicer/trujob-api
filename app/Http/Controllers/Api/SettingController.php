<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingsResource;
use App\Models\Setting;
use Illuminate\Http\Request;

/**
 * Contains api endpoint functions for permission related tasks
 *
 */
class SettingController extends Controller
{

    public function index(Request $request)
    {
        return new SettingsResource(Setting::first());
    }

}
