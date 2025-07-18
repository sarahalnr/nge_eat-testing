<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Platform extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    public function menuPrices()
    {
        return $this->hasMany(MenuPrice::class);
    }
}
