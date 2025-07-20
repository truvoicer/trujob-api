<?php

use App\Models\Country;
use App\Models\Currency;

$country = Country::where('iso2', 'GB')->first();
if (!$country) {
    throw new Exception('Required country not found.');
}
$currency = Currency::where('code', 'GBP')->first();
if (!$currency) {
    throw new Exception('Required currency not found.');
}
$language = $country->languages()->where('iso639_2', 'eng')->first();
if (!$language) {
    throw new Exception('Required language not found.');
}
return [
    [
        'name' => 'tru-job',
        'label' => 'TruJob',
        'description' => 'The PHP Framework For Web Artisans',
        'seo_title' => 'TruJob',
        'seo_description' => 'The PHP Framework For Web Artisans',
        'seo_keywords' => 'laravel, php, framework, web, artisans',
        'author' => 'Taylor Otwell',
        'logo' => 'logo.png',
        'favicon' => 'favicon.png',
        'address' => '123 Street, City, Country',
        'phone' => '+1234567890',
        'email' => null,
        'google_login_client_id' => null,
        'google_tag_manager_id' => null,
        'hubspot_access_token' => null,
        'facebook_app_id' => null,
        'facebook_app_secret' => null,
        'facebook_graph_version' => null,
        'facebook_follow_url' => null,
        'instagram_follow_url' => null,
        'tiktok_follow_url' => null,
        'pinterest_follow_url' => null,
        'x_follow_url' => null,
        'timezone' => 'UTC',
        'settings' => [
            'locale' => 'en',
            'timezone' => 'UTC',
            'currency_id' => $currency->id,
            'country_id' => $country->id,
            'language_id' => $language->id,
            'frontend_url' => 'http://localhost:3000',
        ],
    ]
];
