<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $title = $this->faker->sentence(3);
        $description = $this->faker->sentence(10);
        return [
            'name' => Str::slug($title),
            'label' => $title,
            'description' => $description,
            'seo_title' => $title,
            'seo_description' => $description,
            'seo_keywords' => $this->faker->words(5, true),
            'author' => $this->faker->name(),
            'logo' => 'logo.png',
            'favicon' => 'favicon.png',
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
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
        ];
    }
}
