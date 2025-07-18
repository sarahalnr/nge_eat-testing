<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GoFood extends Model
{
    use HasFactory;
    
    protected $table = 'transaksi_go_food';

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
        return $this->hasMany(GoFoodItem::class, 'transaksi_id');
    }
}