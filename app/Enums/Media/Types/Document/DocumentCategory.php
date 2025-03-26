<?php
namespace App\Enums\Media\Types\Document;

enum DocumentCategory: string
{
    case SPREADSHEET = 'spreadsheet';
    case PRESENTATION = 'presentation';
    case PDF = 'pdf';
    case TEXT = 'text';
    case CODE = 'code';
}