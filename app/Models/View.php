<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $table = 'views';
    protected $fillable = ['name', 'label'];

    public function page()
    {
        return $this->hasMany(Page::class);
    }
}
