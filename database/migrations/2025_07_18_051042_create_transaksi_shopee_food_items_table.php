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
        Schema::create('transaksi_shopee_food_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('menu_price_id');
            $table->unsignedBigInteger('platform_id')->default(1);
            $table->integer('jumlah');
            $table->decimal('harga', 12, 2);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Foreign key definitions
            $table->foreign('transaksi_id')->references('id')->on('transaksi_shopee_food')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreign('menu_price_id')->references('id')->on('menu_prices')->onDelete('cascade');
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
        });
    }


    /**
     * DB::statement("
     * ALTER TABLE transaksi_go_food_items
     * ADD COLUMN subtotal DECIMAL(12,2) GENERATED ALWAYS AS (jumlah * harga) STORED
     *");
     */

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_shopee_food_items');
    }
};
