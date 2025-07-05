<?php

namespace App\Http\Resources\Site;

use App\Http\Resources\MediaResource;
use App\Http\Resources\Site\Setting\SiteSettingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'author' => $this->author,
            'logo' => $this->logo,
            'favicon' => $this->favicon,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'google_login_client_id' => $this->google_login_client_id,
            'google_tag_manager_id' => $this->google_tag_manager_id,
            'hubspot_access_token' => $this->hubspot_access_token,
            'facebook_app_id' => $this->facebook_app_id,
            'facebook_app_secret' => $this->facebook_app_secret,
            'facebook_graph_version' => $this->facebook_graph_version,
            'facebook_follow_url' => $this->facebook_follow_url,
            'instagram_follow_url' => $this->instagram_follow_url,
            'tiktok_follow_url' => $this->tiktok_follow_url,
            'pinterest_follow_url' => $this->pinterest_follow_url,
            'x_follow_url' => $this->x_follow_url,
            'timezone' => $this->timezone,
            'media' => $this->whenLoaded('media', MediaResource::collection($this->media)),
            'settings' => $this->whenLoaded('settings', SiteSettingResource::make($this->settings?->first())),
        ];
    }
}
