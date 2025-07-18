<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\GoFood;
use App\Models\GoFoodItem;
use App\Models\Menu;
use App\Models\Platform;
use App\Models\MenuPrice;
use App\Models\Category; // Tambahkan ini jika belum ada

class UnitTest extends TestCase
{
    // Menggunakan RefreshDatabase untuk memastikan database bersih di setiap test
    use RefreshDatabase, WithFaker;

    /**
     * Set up the test environment.
     * Membuat data dummy yang dibutuhkan untuk pengujian.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Buat kategori dan platform terlebih dahulu karena mereka adalah dependensi
        Category::factory()->create(); // Contoh, Anda mungkin ingin membuat beberapa kategori
        Platform::factory()->create(['name' => 'GoFood']); // Buat platform GoFood
        Platform::factory()->create(['name' => 'GrabFood']); // Buat platform lain jika perlu
        
        // Buat beberapa menu dengan harga di platform yang berbeda
        $this->menu1 = Menu::factory()->create(['name' => 'Nasi Goreng']);
        $this->platform1 = Platform::first(); // Ambil platform pertama
        $this->menuPrice1 = MenuPrice::factory()->create([
            'menu_id' => $this->menu1->id,
            'platform_id' => $this->platform1->id,
            'price' => 25000,
        ]);

        $this->menu2 = Menu::factory()->create(['name' => 'Mie Ayam']);
        $this->platform2 = Platform::skip(1)->first() ?? Platform::factory()->create(); // Pastikan ada platform kedua
        $this->menuPrice2 = MenuPrice::factory()->create([
            'menu_id' => $this->menu2->id,
            'platform_id' => $this->platform2->id,
            'price' => 20000,
        ]);
    }

    /**
     * Test the store method with valid data.
     * @return void
     */
    public function test_store_method_creates_a_new_transaction_successfully()
    {
        $data = [
            'tanggal' => '2025-07-17',
            'waktu' => '10:00',
            'nama_pelanggan' => 'John Doe',
            'metode_pembayaran' => 'Cash',
            'items' => [
                [
                    'menu_id' => $this->menu1->id,
                    'platform_id' => $this->platform1->id,
                    'jumlah' => 2,
                ],
                [
                    'menu_id' => $this->menu2->id,
                    'platform_id' => $this->platform2->id,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->post(route('gofood.store'), $data);

        // Memastikan redirect sukses dengan session 'success'
        $response->assertRedirect(route('gofood.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil ditambahkan!');

        // Memastikan data tersimpan di database
        $this->assertDatabaseHas('transaksi_go_food', [
            'nama_pelanggan' => 'John Doe',
            'total' => ($this->menuPrice1->price * 2) + ($this->menuPrice2->price * 1), // 50000 + 20000 = 70000
            'jumlah' => 3,
        ]);

        // Memastikan item transaksi tersimpan
        $this->assertDatabaseCount('transaksi_go_food_items', 2);
        $this->assertDatabaseHas('transaksi_go_food_items', [
            'menu_id' => $this->menu1->id,
            'jumlah' => 2,
            'harga' => $this->menuPrice1->price,
        ]);
        $this->assertDatabaseHas('transaksi_go_food_items', [
            'menu_id' => $this->menu2->id,
            'jumlah' => 1,
            'harga' => $this->menuPrice2->price,
        ]);
    }

    /**
     * Test the store method with invalid data (validation failure).
     * @return void
     */
    public function test_store_method_returns_validation_errors_for_invalid_data()
    {
        $data = [
            'tanggal' => 'invalid-date', // Invalid
            'waktu' => '', // Missing
            'nama_pelanggan' => '', // Missing
            'metode_pembayaran' => 'Cash',
            'items' => [], // Empty items
        ];

        $response = $this->post(route('gofood.store'), $data);

        // Memastikan redirect kembali dengan error validasi
        $response->assertSessionHasErrors(['tanggal', 'waktu', 'nama_pelanggan', 'items']);
        $response->assertStatus(302); // Redirect back
    }

    /**
     * Test the store method when MenuPrice is not found.
     * @return void
     */
    public function test_store_method_returns_error_if_menu_price_not_found()
    {
        $nonExistentMenuId = Menu::factory()->create()->id; // Buat menu baru
        $nonExistentPlatformId = Platform::factory()->create()->id; // Buat platform baru

        // Jangan buat MenuPrice untuk kombinasi ini
        $data = [
            'tanggal' => '2025-07-17',
            'waktu' => '11:00',
            'nama_pelanggan' => 'Jane Doe',
            'metode_pembayaran' => 'OVO',
            'items' => [
                [
                    'menu_id' => $nonExistentMenuId,
                    'platform_id' => $nonExistentPlatformId,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->post(route('gofood.store'), $data);

        // Memastikan redirect kembali dengan session 'error'
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Gagal menyimpan transaksi: Harga tidak ditemukan.');
        $this->assertDatabaseCount('transaksi_go_food', 0); // Memastikan tidak ada transaksi yang tersimpan
        $this->assertDatabaseCount('transaksi_go_food_items', 0); // Memastikan tidak ada item yang tersimpan
    }

    /**
     * Test the update method with valid data.
     * @return void
     */
    public function test_update_method_updates_a_transaction_successfully()
    {
        // Buat transaksi dan item lama
        $oldTransaction = GoFood::factory()->create([
            'nama_pelanggan' => 'Old Customer',
            'total' => 10000,
            'jumlah' => 1,
        ]);
        GoFoodItem::factory()->create([
            'transaksi_id' => $oldTransaction->id,
            'menu_id' => $this->menu1->id,
            'platform_id' => $this->platform1->id,
            'menu_price_id' => $this->menuPrice1->id,
            'harga' => $this->menuPrice1->price,
            'jumlah' => 1,
        ]);

        $updatedData = [
            'tanggal' => '2025-07-18',
            'waktu' => '14:30',
            'nama_pelanggan' => 'Updated Customer',
            'metode_pembayaran' => 'Card',
            'status' => true,
            'items' => [
                [
                    'menu_id' => $this->menu1->id, // Tetap menu 1
                    'platform_id' => $this->platform1->id,
                    'jumlah' => 3, // Ubah jumlah
                ],
                [
                    'menu_id' => $this->menu2->id, // Tambah menu 2
                    'platform_id' => $this->platform2->id,
                    'jumlah' => 2,
                ],
            ],
        ];

        $response = $this->put(route('gofood.update', $oldTransaction->id), $updatedData);

        // Memastikan redirect sukses dengan session 'success'
        $response->assertRedirect(route('gofood.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil diperbarui!');

        // Memastikan data transaksi di database sudah terupdate
        $this->assertDatabaseHas('transaksi_go_food', [
            'id' => $oldTransaction->id,
            'nama_pelanggan' => 'Updated Customer',
            'total' => ($this->menuPrice1->price * 3) + ($this->menuPrice2->price * 2), // 75000 + 40000 = 115000
            'jumlah' => 5,
            'status' => true,
        ]);

        // Memastikan item lama sudah dihapus dan item baru tersimpan
        $this->assertDatabaseCount('transaksi_go_food_items', 2); // Hanya ada 2 item baru
        $this->assertDatabaseHas('transaksi_go_food_items', [
            'transaksi_id' => $oldTransaction->id,
            'menu_id' => $this->menu1->id,
            'jumlah' => 3,
        ]);
        $this->assertDatabaseHas('transaksi_go_food_items', [
            'transaksi_id' => $oldTransaction->id,
            'menu_id' => $this->menu2->id,
            'jumlah' => 2,
        ]);
    }

    /**
     * Test the update method with invalid data (validation failure).
     * @return void
     */
    public function test_update_method_returns_validation_errors_for_invalid_data()
    {
        $transaction = GoFood::factory()->create();

        $invalidData = [
            'tanggal' => 'baddate',
            'waktu' => '',
            'items' => [],
        ];

        $response = $this->put(route('gofood.update', $transaction->id), $invalidData);

        $response->assertSessionHasErrors(['tanggal', 'waktu', 'items']);
        $response->assertStatus(302);
    }

    /**
     * Test the destroy method for a found transaction.
     * @return void
     */
    public function test_destroy_method_deletes_a_transaction_successfully()
    {
        // Buat transaksi dan item terkait
        $transaction = GoFood::factory()->create();
        GoFoodItem::factory()->count(2)->create(['transaksi_id' => $transaction->id]);

        $this->assertDatabaseHas('transaksi_go_food', ['id' => $transaction->id]);
        $this->assertDatabaseHas('transaksi_go_food_items', ['transaksi_id' => $transaction->id]);

        $response = $this->delete(route('gofood.destroy', $transaction->id));

        $response->assertRedirect(route('gofood.index'));
        $response->assertSessionHas('success', 'Transaksi berhasil dihapus.');

        // Memastikan transaksi dan item-nya sudah dihapus dari database
        $this->assertDatabaseMissing('transaksi_go_food', ['id' => $transaction->id]);
        $this->assertDatabaseMissing('transaksi_go_food_items', ['transaksi_id' => $transaction->id]);
    }

    /**
     * Test the destroy method for a non-existent transaction.
     * @return void
     */
    public function test_destroy_method_redirects_with_error_for_non_existent_transaction()
    {
        $nonExistentId = 999; // ID yang tidak ada

        $response = $this->delete(route('gofood.destroy', $nonExistentId));

        $response->assertRedirect(route('gofood.index'));
        $response->assertSessionHas('error', 'Transaksi tidak ditemukan.');
    }
}