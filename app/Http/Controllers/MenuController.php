<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Platform;
use App\Models\MenuPrice;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Tampilkan daftar menu
    public function index(Request $request)
    {
        $query = Menu::with(['category', 'prices.platform'])->orderBy('id', 'desc');
        // Filter berdasarkan kategori jika ada
        if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
        }

        $menus = $query->paginate(10)->withQueryString(); // Agar pagination mempertahankan filter

        $categories = Category::all();
        $platforms = Platform::all();
        $page = $request->get('page', 1);
        $selectedCategory = $request->category_id;

        return view('menus.index', compact('menus', 'categories', 'platforms', 'page', 'selectedCategory'));
    }


    // Simpan menu baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description ?? null,
        ]);

        foreach ($request->prices as $platform_id => $price) {
            MenuPrice::create([
                'menu_id' => $menu->id,
                'platform_id' => $platform_id,
                'price' => $price,
            ]);
        }

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    // Mengambil data menu untuk API / debug
    public function get($id)
    {
        $menu = Menu::with(['prices', 'category'])->findOrFail($id);
        return response()->json($menu);
    }

    // Menampilkan form edit via modal (AJAX)
    public function editModal(Menu $menu, Request $request)
    {
        $categories = Category::all();
        $platforms = Platform::all();
        $menu->load('prices');

        return view('components.menu-form-edit', compact('menu', 'categories', 'platforms'));
    }

    // Update menu
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

        $menu->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description ?? null,
        ]);

        foreach ($request->prices as $platform_id => $price) {
            $menu->prices()->updateOrCreate(
                ['platform_id' => $platform_id],
                ['price' => $price]
            );
        }

        $page = $request->query('page', 1);
        return redirect()->route('menus.index', ['page' => $page])
                        ->with('success', 'Menu berhasil diupdate.');
    }

    // Hapus menu
    public function destroy(Menu $menu)
    {
        try {
            $menu->prices()->delete();
            $menu->delete();

            return redirect()->back()->with('success', 'Menu berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus menu: ' . $e->getMessage());
        }
    }
}
