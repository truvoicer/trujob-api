<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class EditSiteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'seo_title' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'seo_description' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'seo_keywords' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'author' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'logo' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'favicon' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'address' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'email' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'google_login_client_id' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'google_tag_manager_id' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'hubspot_access_token' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'facebook_app_id' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'facebook_app_secret' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'facebook_graph_version' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'facebook_follow_url' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'instagram_follow_url' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'tiktok_follow_url' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'pinterest_follow_url' => [
                'sometimes',
                'string',
                'max:255',
            ],
            'x_follow_url' => [
                'sometimes',
                'string',
                'max:255',
            ],
        ];
    }
}
