<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        'product_id',
        'notes',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'status',
        'is_public',
        'is_all_day',
        'is_recurring',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_public' => 'boolean',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
