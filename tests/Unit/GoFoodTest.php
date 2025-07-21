<?php

namespace Tests\Unit;

use App\Models\GoFood;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\QueryException; // Import QueryException

class GoFoodTest extends TestCase
{
    use RefreshDatabase; // Memastikan database di-refresh untuk setiap pengujian

    /**
     * Menguji penambahan data GoFood dengan data yang valid dan sukses.
     *
     * @return void
     */
    public function test_tambah_dengann_valid_sukses()
    {
        // Data yang valid untuk membuat entri GoFood baru
        $dataValid = [
            'id_pesanan' => 'GF-20250721-001',
            'tanggal' => '2025-07-21',
            'waktu' => '10:30:00',
            'nama_pelanggan' => 'Budi Santoso',
            'total' => 75000,
            'metode_pembayaran' => 'OVO',
            'status' => 1, // true untuk Selesai, false untuk Belum Selesai
            'jumlah' => 2,
        ];

        // Membuat entri GoFood menggunakan model
        $goFood = GoFood::create($dataValid);

        // Memastikan bahwa entri telah berhasil disimpan di database
        $this->assertDatabaseHas('transaksi_go_food', [
            'id_pesanan' => 'GF-20250721-001',
            'nama_pelanggan' => 'Budi Santoso',
            'total' => 75000,
        ]);

        // Memastikan bahwa objek model yang dibuat memiliki atribut yang benar
        $this->assertInstanceOf(GoFood::class, $goFood);
        $this->assertEquals('GF-20250721-001', $goFood->id_pesanan);
        $this->assertEquals('Budi Santoso', $goFood->nama_pelanggan);
        $this->assertEquals(75000, $goFood->total);
        $this->assertTrue($goFood->status); // Memastikan status adalah boolean true
        $this->assertEquals(2, $goFood->jumlah);
    }

    /**
     * Menguji penambahan data GoFood dengan salah satu form kosong (misalnya, total).
     * Ini akan menguji bagaimana model bereaksi terhadap data yang tidak valid
     * yang mungkin melanggar batasan `NOT NULL` di database.
     *
     * Catatan: Jika kolom 'total' di database Anda mengizinkan NULL,
     * tes ini mungkin perlu disesuaikan atau dipindahkan ke pengujian fitur
     * yang mencakup validasi form pada level controller/request.
     * Untuk tujuan demonstrasi, kita mengasumsikan 'total' adalah NOT NULL.
     *
     * @return void
     */
    public function test_tambah_salah_satu_form_kosong()
    {
        // Data tidak valid: 'total' diset menjadi null (atau string kosong)
        // yang akan melanggar batasan NOT NULL di database jika ada.
        $dataTidakValid = [
            'id_pesanan' => 'GF-20250721-002',
            'tanggal' => '2025-07-21',
            'waktu' => '11:00:00',
            'nama_pelanggan' => 'Siti Aminah',
            'total' => null, // Ini adalah kolom yang dikosongkan
            'metode_pembayaran' => 'Cash',
            'status' => false,
            'jumlah' => 1,
        ];

        // Mengharapkan QueryException karena pelanggaran batasan NOT NULL
        // jika kolom 'total' di database Anda memang NOT NULL.
        $this->expectException(QueryException::class);

        // Mencoba membuat entri GoFood dengan data tidak valid
        GoFood::create($dataTidakValid);

        // Memastikan bahwa entri tidak disimpan di database
        $this->assertDatabaseMissing('transaksi_go_food', [
            'id_pesanan' => 'GF-20250721-002',
        ]);
    }
}
