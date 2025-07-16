<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrabFood extends Model
{
    protected $table = 'transaksi_grab_food';

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
        return $this->hasMany(GrabFoodItem::class, 'transaksi_id');
    }
}