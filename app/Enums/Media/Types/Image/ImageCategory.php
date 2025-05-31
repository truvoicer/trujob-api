<?php
namespace App\Enums\Media\Types\Image;

enum ImageCategory: string
{
    case THUMBNAIL = 'thumbnail';
    case GALLERY = 'gallery';
    case FEATURED = 'featured';
    case HERO = 'hero';
    case ICON = 'icon';
    case LOGO = 'logo';
    case BANNER = 'banner';
    case AVATAR = 'avatar';
    case FAVICON = 'favicon';
    case PRODUCT_IMAGE = 'product_image';
}