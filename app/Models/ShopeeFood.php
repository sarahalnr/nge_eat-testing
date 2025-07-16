<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopeeFood extends Model
{
    protected $table = 'transaksi_shopee_food';

    protected $fillable = [
        'id_pesanan',
        'tanggal',
        'waktu',
        'nama_pelanggan',
        'total',
        'metode_pembayaran',
        'status',
        'jumlah' 
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime:H:i:s',
        'status' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(ShopeeFoodItem::class, 'transaksi_id');
    }
}