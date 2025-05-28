<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiscountUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_id',
        'user_id',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incrementUsage()
    {
        $this->update([
            'usage_count' => $this->usage_count + 1,
            'last_used_at' => now(),
        ]);
        
    }
    
    public function getRemainingUsesAttribute()
    {
        return $this->discount?->usage_limit ? $this->discount->usage_limit - $this->discount->usage_count : null;
    }
    public function getUserRemainingUsesAttribute()
    {
        return $this->discount?->per_user_limit ? $this->discount->per_user_limit - $this->usage_count : null;
    }
        
}
