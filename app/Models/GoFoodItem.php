<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoFoodItem extends Model
{
    use HasFactory;
    
    protected $table = 'transaksi_go_food_items';

    protected $fillable = [
        'transaksi_id',
        'menu_id',
        'menu_price_id',
        'platform_id',
        'harga',
        'jumlah',
    ];

    public function transaksi()
    {
        return $this->belongsTo(GoFood::class, 'transaksi_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function menuPrice()
    {
        return $this->belongsTo(MenuPrice::class, 'menu_price_id');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id');
    }
}