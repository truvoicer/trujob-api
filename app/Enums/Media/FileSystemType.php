<?php
namespace App\Enums\Media;

enum FileSystemType: string
{
    case LOCAL = 'local';
    case S3 = 's3';
    case PUBLIC = 'public';
    case EXTERNAL = 'external';
}
