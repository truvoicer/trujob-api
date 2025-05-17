<?php

use App\Jobs\DefaultSiteData;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    DefaultSiteData::dispatch();
})->everyFiveMinutes();

