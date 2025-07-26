<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FileDownload>
 */
class FileDownloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'download_key' => $this->faker->unique()->sha256,
            'client_ip' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
        ];
    }
}
