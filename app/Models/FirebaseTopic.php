<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebaseTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    const DEFAULT_TOPIC = 'all';

    public function devices() {
        return $this->hasMany(FirebaseDevice::class);
    }
}
