<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirebaseDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'register_token'
    ];

    public function topics() {
        return $this->belongsTo(FirebaseTopic::class);
    }
}
