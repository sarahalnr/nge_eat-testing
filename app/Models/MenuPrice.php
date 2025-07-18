<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuPrice extends Model
{
    use HasFactory;
    
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
