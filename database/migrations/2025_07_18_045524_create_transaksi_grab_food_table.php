<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_grab_food', function (Blueprint $table) {
            $table->id(); // int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('id_pesanan', 100);
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('nama_pelanggan', 100)->nullable();
            $table->string('metode_pembayaran', 50)->nullable();
            $table->decimal('total', 12, 2)->default(0.00);
            $table->unsignedInteger('jumlah')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_grab_food');
    }
};
