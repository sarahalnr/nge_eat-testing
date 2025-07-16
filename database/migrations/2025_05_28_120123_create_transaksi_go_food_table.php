<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksi_go_food', function (Blueprint $table) {
            $table->id();
            $table->string('id_pesanan');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('nama_pelanggan');
            $table->text('item_pesanan');
            $table->integer('total');
            $table->string('metode_pembayaran');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_go_food');
    }
};
