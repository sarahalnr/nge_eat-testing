<?php

namespace App\Http\Controllers;

use App\Models\GoFood;
use App\Models\GoFoodItem;
use App\Models\Platform;
use App\Models\Menu;
use App\Models\MenuPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoFoodController extends Controller
{
    public function index(Request $request)
    {
        $query = GoFood::with(['items.menu'])->latest();

        // ✅ Filter tanggal dari query ?tanggal=YYYY-MM-DD
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $transaksi = $query->paginate(10)->appends($request->except('page'));
        $platforms = Platform::all();
        $menus = Menu::all();
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

    private function generateIdPesanan(): string
    {
        do {
            $id = 'GOFO' . strtoupper(Str::random(8)); 
        } while (GoFood::where('id_pesanan', $id)->exists());

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

            $transaksi = GoFood::create([
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

                GoFoodItem::create([
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

            return redirect()->route('gofood.index')->with('success', 'Transaksi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $transaksi = GoFood::with('items')->findOrFail($id);
        $platforms = Platform::all();
        $menus = Menu::all();

        return view('gofood.edit', compact('transaksi', 'platforms', 'menus'));
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
            $transaksi = GoFood::findOrFail($id);
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

                GoFoodItem::create([
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

            return redirect()->route('gofood.index')->with('success', 'Transaksi berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            $page = $request->query('page', 1); 
            return redirect()->route('gofood.index', ['page' => $page])
                ->with('success', 'Transaksi berhasil diperbarui');
        }
    }

    public function destroy($id)
    {
        $transaksi = GoFood::find($id);

        if (!$transaksi) {
            return redirect()->route('gofood.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        $transaksi->items()->delete();
        $transaksi->delete();

        return redirect()->route('gofood.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    public function editJson($id)
    {
        $transaksi = GoFood::with(['items.menu', 'items.platform'])->findOrFail($id);

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
