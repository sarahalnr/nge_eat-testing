<?php

namespace App\Http\Controllers;

use App\Models\GoFood;
use App\Models\Platform;
use App\Models\Menu;
use App\Models\MenuPrice;
use App\Services\GoFoodService; // <-- Import Service
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Masih dipakai jika generateIdPesanan tetap di controller

class GoFoodController extends Controller
{
    protected $goFoodService;

    // Inject GoFoodService melalui constructor
    public function __construct(GoFoodService $goFoodService)
    {
        $this->goFoodService = $goFoodService;
    }

    public function index(Request $request)
    {
        $query = GoFood::with(['items.menu'])->latest();

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $transaksi = $query->paginate(10)->appends($request->except('page'));
        $platforms = Platform::all();
        $menus = Menu::all();
        
        // generateIdPesanan() bisa dari service atau tetap di sini jika hanya untuk view
        // Jika Anda memindahkannya ke service, maka panggil $this->goFoodService->generateIdPesanan();
        $generatedId = $this->generateIdPesanan(); 

        return view('gofood.index', compact('transaksi', 'platforms', 'menus', 'generatedId'));
    }

    public function getAll()
    {
        return response()->json(
            GoFood::with('items.menu', 'items.platform')->latest()->get()
        );
    }

    public function getPrice(Request $request)
    {
        $price = MenuPrice::where('menu_id', $request->menu_id)
            ->where('platform_id', $request->platform_id)
            ->first();

        return response()->json(['price' => $price?->price ?? 0]);
    }

    // Ini bisa dipindahkan ke GoFoodService jika digunakan untuk ID transaksi saat membuat.
    // Namun, karena sudah ada di service, ini bisa dihapus atau disesuaikan.
    // Jika ini hanya untuk tampilan form awal, biarkan di sini.
    private function generateIdPesanan(): string
    {
        do {
            $id = 'GOFO' . strtoupper(Str::random(8)); 
        } while (GoFood::where('id_pesanan', $id)->exists());

        return $id;
    }

    public function store(Request $request)
    {
        // Validasi tetap di controller
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'nama_pelanggan' => 'required',
            'metode_pembayaran' => 'required',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.platform_id' => 'required|exists:platforms,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            // Panggil service untuk membuat transaksi
            $this->goFoodService->createTransaction($request->all());

            return redirect()->route('gofood.index')->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function edit(GoFood $transaksi) // Menggunakan Route Model Binding
    {
        // $transaksi sudah otomatis dicari oleh Laravel berdasarkan ID di route
        $platforms = Platform::all();
        $menus = Menu::all();

        return view('gofood.edit', compact('transaksi', 'platforms', 'menus'));
    }

    // Perbarui method update agar menggunakan Route Model Binding
    public function update(Request $request, GoFood $transaksi) // Menggunakan Route Model Binding
    {
        // Validasi tetap di controller
        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'nama_pelanggan' => 'required',
            'metode_pembayaran' => 'required',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.platform_id' => 'required|exists:platforms,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            // Panggil service untuk mengupdate transaksi
            $this->goFoodService->updateTransaction($transaksi, $request->all());

            return redirect()->route('gofood.index')->with('success', 'Transaksi berhasil diperbarui!');
        } catch (\Exception $e) {
            $page = $request->query('page', 1); 
            // BUGFIX: Mengembalikan error, bukan success
            return redirect()->route('gofood.index', ['page' => $page])
                ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage()); 
        }
    }

    // Perbarui method destroy agar menggunakan Route Model Binding
    public function destroy(GoFood $transaksi) // Menggunakan Route Model Binding
    {
        try {
            // Panggil service untuk menghapus transaksi
            $this->goFoodService->deleteTransaction($transaksi);

            return redirect()->route('gofood.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            // Jika transaksi tidak ditemukan oleh Route Model Binding, Laravel otomatis melempar 404
            // Ini menangani error dari service
            return redirect()->route('gofood.index')->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    public function editJson(GoFood $transaksi) // Menggunakan Route Model Binding
    {
        $items = $transaksi->items->map(function ($item) {
            return [
                'platform_id' => $item->platform_id,
                'jumlah'      => $item->jumlah,
                'harga'       => $item->harga,
                'subtotal'    => $item->harga * $item->jumlah,
                'menu_id'     => $item->menu_id,
            ];
        })->toArray();

        return response()->json([
            'waktu'             => $transaksi->waktu ? \Carbon\Carbon::parse($transaksi->waktu)->format('H:i') : '',
            'id_pesanan'        => $transaksi->id_pesanan,
            'nama_pelanggan'    => $transaksi->nama_pelanggan,
            'metode_pembayaran' => $transaksi->metode_pembayaran,
            'total'             => $transaksi->total,
            'status'            => $transaksi->status,
            'items'             => $items,
            'tanggal'           => $transaksi->tanggal ? $transaksi->tanggal->format('Y-m-d') : '',
        ]);
    }
}