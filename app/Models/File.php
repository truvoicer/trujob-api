<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'full_path',
        'rel_path',
        'extension',
        'type',
        'size',
        'file_system',
        'mime_type'
    ];

    public function fileDownloads()
    {
        return $this->hasMany(FileDownload::class);
    }
}
