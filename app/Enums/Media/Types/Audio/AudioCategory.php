<?php
namespace App\Enums\Media\Types\Audio;

enum AudioCategory: string
{
    case MUSIC = 'music';
    case PODCAST = 'podcast';
    case AUDIOBOOK = 'audiobook';
    case OTHER = 'other';
}