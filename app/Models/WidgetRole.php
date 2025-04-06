<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetRole extends Model
{
    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
