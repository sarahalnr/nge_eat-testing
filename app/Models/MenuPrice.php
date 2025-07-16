<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuPrice extends Model
{
    protected $fillable = ['menu_id', 'platform_id', 'price'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
