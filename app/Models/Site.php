<?php

namespace App\Models;

use App\Enums\Payment\PaymentGateway as PaymentGatewayEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\HasApiTokens;

class Site extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'author',
        'logo',
        'favicon',
        'address',
        'phone',
        'email',
        'google_login_client_id',
        'google_tag_manager_id',
        'hubspot_access_token',
        'facebook_app_id',
        'facebook_app_secret',
        'facebook_graph_version',
        'facebook_follow_url',
        'instagram_follow_url',
        'tiktok_follow_url',
        'pinterest_follow_url',
        'x_follow_url',
        'timezone',
    ];

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status', 'id')
            ->withTimestamps();
    }

    public function sidebars()
    {
        return $this->hasMany(Sidebar::class);
    }
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function widgets()
    {
        return $this->hasMany(Widget::class);
    }

    public function settings()
    {
        return $this->hasOne(SiteSetting::class);
    }

    public function siteUsers(): MorphMany
    {
        return $this->morphMany(SiteUser::class, 'siteUserable');
    }

    public function activePaymentGatewayByName(PaymentGatewayEnum $gateway): BelongsToMany
    {
        return $this->paymentGateways()
            ->where('name', $gateway->value)
            ->where('payment_gateway_sites.is_active', true);
    }
    public function paymentGateways()
    {
        return $this->belongsToMany(PaymentGateway::class, 'payment_gateway_sites')
        ->using(PaymentGatewaySite::class)
        ->withPivot('settings', 'is_active', 'is_default', 'environment')
            ->withTimestamps();
    }
}
