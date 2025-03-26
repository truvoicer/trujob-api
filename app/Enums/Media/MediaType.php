<?php
namespace App\Enums\Media;

use App\Enums\Media\Types\Audio\AudioCategory;
use App\Enums\Media\Types\Document\DocumentCategory;
use App\Enums\Media\Types\Image\ImageCategory;

enum MediaType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
    case DOCUMENT = 'document';
    case ARCHIVE = 'archive';
    case OTHER = 'other';

    public function getCategories(): array
    {
        return match ($this) {
            self::IMAGE => ImageCategory::cases(),
            self::AUDIO => AudioCategory::cases(),
            self::DOCUMENT => DocumentCategory::cases(),
            default => [],
        };
    }
}