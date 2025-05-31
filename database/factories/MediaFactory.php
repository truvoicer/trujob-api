<?php

namespace Database\Factories;

use App\Enums\Media\FileSystemType;
use App\Enums\Media\MediaCategory;
use App\Enums\Media\MediaType;
use App\Enums\Media\Types\Audio\AudioCategory;
use App\Enums\Media\Types\Document\DocumentCategory;
use App\Enums\Media\Types\Image\ImageCategory;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductMedia>
 */
class MediaFactory extends Factory
{
    private string $loremPicsumUrl = 'https://picsum.photos/id';

    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $randomNumberBetween = random_int(1, 100);
        $filesystem = fake()->randomElement(array_map(fn($type) => $type->value, FileSystemType::cases()));
        $type = fake()->randomElement(array_map(fn($type) => $type->value, MediaType::cases()));
        $category = null;
        $path = null;
        $url = null;
        switch ($type) {
            case MediaType::IMAGE->value:
                $category = fake()->randomElement(array_map(fn($type) => $type->value, ImageCategory::cases()));
                break;
            case MediaType::AUDIO->value:
                $category = fake()->randomElement(array_map(fn($type) => $type->value, AudioCategory::cases()));
                break;
            case MediaType::DOCUMENT->value:
                $category = fake()->randomElement(array_map(fn($type) => $type->value, DocumentCategory::cases()));
                break;
        }
        switch ($filesystem) {
            case FileSystemType::EXTERNAL->value:
                switch ($category) {
                    case ImageCategory::LISTING_IMAGE->value:
                        $url = "{$this->loremPicsumUrl}/{$randomNumberBetween}/700/700";
                        break;
                    default:
                        $url = "{$this->loremPicsumUrl}/{$randomNumberBetween}/300/300";
                        break;
                }
                break;
            default:
                $path = "{$this->loremPicsumUrl}/{$randomNumberBetween}/300/300";
                break;
        }
        return [
            'type' => $type,
            'filesystem' => $filesystem,
            'category' => $category,
            'alt' => fake()->text(20),
            'path' => $path,
            'url' => $url,
        ];
    }
}
