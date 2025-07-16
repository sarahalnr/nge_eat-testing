<?php

namespace App\Http\Controllers;

use App\Models\ShopeeFood;
use App\Models\ShopeeFoodItem;
use App\Models\Platform;
use App\Models\Menu;
use App\Models\MenuPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopeeFoodController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopeeFood::with(['items.menu'])->latest();

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $transaksi = $query->paginate(10)->appends($request->except('page'));
        $platforms = Platform::all();
        $menus = Menu::all();
        $generatedId = $this->generateIdPesanan();

        return view('shopeefood.index', compact('transaksi', 'platforms', 'menus', 'generatedId'));
    }

    public function getAll()
    {
        return response()->json(
            ShopeeFood::with('items.menu', 'items.platform')->latest()->get()
        );
    }

    public function getPrice(Request $request)
    {
        $price = MenuPrice::where('menu_id', $request->menu_id)
            ->where('platform_id', $request->platform_id)
            ->first();

        return response()->json(['price' => $price?->price ?? 0]);
    }

    private function generateIdPesanan(): string
    {
        do {
            $id = 'SHOPPE' . strtoupper(Str::random(8)); // GOFO + 8 huruf/angka acak
        } while (ShopeeFood::where('id_pesanan', $id)->exists());

        return $id;
    }

    public function store(Request $request)
    {
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

        DB::beginTransaction();

        try {
            $total = 0;
            $jumlahTotalItem = 0;

            $transaksi = ShopeeFood::create([
                'id_pesanan' => $this->generateIdPesanan(),
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'nama_pelanggan' => $request->nama_pelanggan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $request->has('status') ? 1 : 0,
                'total' => 0,
                'jumlah' => 0,
            ]);

            foreach ($request->items as $item) {
                $menuPrice = MenuPrice::where('menu_id', $item['menu_id'])
                    ->where('platform_id', $item['platform_id'])
                    ->first();

                if (!$menuPrice) {
                    throw new \Exception('Harga tidak ditemukan.');
                }

                $subtotal = $menuPrice->price * $item['jumlah'];
                $total += $subtotal;
                $jumlahTotalItem += $item['jumlah'];

                ShopeeFoodItem::create([
                    'transaksi_id' => $transaksi->id,
                    'menu_id' => $item['menu_id'],
                    'menu_price_id' => $menuPrice->id,
                    'platform_id' => $item['platform_id'],
                    'harga' => $menuPrice->price,
                    'jumlah' => $item['jumlah'],
                ]);
            }

            $transaksi->update([
                'total' => $total,
                'jumlah' => $jumlahTotalItem,
            ]);

            DB::commit();

            return redirect()->route('shopeefood.index')->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $transaksi = ShopeeFood::with('items')->findOrFail($id);
        $platforms = Platform::all();
        $menus = Menu::all();

        return view('shopeefood.edit', compact('transaksi', 'platforms', 'menus'));
    }

    public function update(Request $request, $id)
    {
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

        DB::beginTransaction();

        try {
            $transaksi = ShopeeFood::findOrFail($id);
            $transaksi->items()->delete();

            $total = 0;
            $jumlahTotalItem = 0;

            foreach ($request->items as $item) {
                $menuPrice = MenuPrice::where('menu_id', $item['menu_id'])
                    ->where('platform_id', $item['platform_id'])
                    ->first();

                if (!$menuPrice) {
                    throw new \Exception('Harga tidak ditemukan.');
                }

                $subtotal = $menuPrice->price * $item['jumlah'];
                $total += $subtotal;
                $jumlahTotalItem += $item['jumlah'];

                ShopeeFoodItem::create([
                    'transaksi_id' => $transaksi->id,
                    'menu_id' => $item['menu_id'],
                    'menu_price_id' => $menuPrice->id,
                    'platform_id' => $item['platform_id'],
                    'harga' => $menuPrice->price,
                    'jumlah' => $item['jumlah'],
                ]);
            }

            $transaksi->update([
                'tanggal' => $request->tanggal,
                'waktu' => $request->waktu,
                'nama_pelanggan' => $request->nama_pelanggan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $request->has('status') ? 1 : 0,
                'total' => $total,
                'jumlah' => $jumlahTotalItem,
            ]);

            DB::commit();

            return redirect()->route('shopeefood.index')->with('success', 'Transaksi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            $page = $request->query('page', 1); // default ke halaman 1 jika tidak ada
                return redirect()->route('shopeefood.index', ['page' => $page])
                    ->with('success', 'Transaksi berhasil diperbarui');
        }
    }

    public function destroy($id)
    {
        $transaksi = ShopeeFood::find($id);

        if (!$transaksi) {
        return redirect()->route('shopeefood.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        $transaksi->items()->delete();
        $transaksi->delete();

    return redirect()->route('shopeefood.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function editJson($id)
    {
        $transaksi = ShopeeFood::with(['items.menu', 'items.platform'])->findOrFail($id);

        $items = $transaksi->items->map(function($item) {
        // Siapkan array item untuk frontend
            return [
                'platform_id' => $item->platform_id,
                'jumlah'      => $item->jumlah,
                'harga'       => $item->harga,
                'subtotal'    => $item->harga * $item->jumlah,
                'menu_id'     => $item->menu_id,
            ];
        })->toArray();

        return response()->json([
            'waktu'              => $transaksi->waktu ? \Carbon\Carbon::parse($transaksi->waktu)->format('H:i') : '',
            'id_pesanan'         => $transaksi->id_pesanan,
            'nama_pelanggan'     => $transaksi->nama_pelanggan,
            'metode_pembayaran'  => $transaksi->metode_pembayaran,
            'total'              => $transaksi->total,
            'status'             => $transaksi->status,
            'items'              => $items,
            'tanggal'            => $transaksi->tanggal ? $transaksi->tanggal->format('Y-m-d') : '',
        ]);
    }
}