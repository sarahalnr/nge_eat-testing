<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrabFoodItem extends Model
{
    protected $table = 'transaksi_grab_food_items';

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
        return $this->belongsTo(GrabFood::class, 'transaksi_id');
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