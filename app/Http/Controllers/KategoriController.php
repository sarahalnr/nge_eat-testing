<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 1);

        $categories = Kategori::orderBy('id', 'desc')
            ->paginate(10)
            ->appends(['page' => $page]);

        return view('kategori.index', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Kategori::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $category = Kategori::findOrFail($id);
    $category->update([
        'name' => $request->name,
    ]);

    $page = $request->query('page', 1); 
    return redirect()->route('kategori.index', ['page' => $page])
                ->with('success', 'Kategori berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $category = Kategori::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function get($id)
    {
        $category = Kategori::findOrFail($id);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json($category);
        }

        return redirect()->route('kategori.index');
    }

    public function getAll()
    {
        $categories = Kategori::all();
        return response()->json($categories);
    }
}
