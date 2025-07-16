<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoFoodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\GrabFoodController;
use App\Http\Controllers\ShopeeFoodController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ItemTerjualController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DownloadController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route Kategori
Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
Route::get('/kategori/{id}', [KategoriController::class, 'get'])->name('kategori.get'); 
Route::get('/api/kategori', [KategoriController::class, 'getAll'])->name('kategori.api'); 

// Route Menu
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
Route::get('/menus/{menu}/edit-modal', [MenuController::class, 'editModal'])->name('menus.editModal');

// Route Gofood
Route::get('/gofood', [GoFoodController::class, 'index'])->name('gofood.index');
Route::get('/api/gofood', [GoFoodController::class, 'getAll']);
Route::post('/gofood', [GoFoodController::class, 'store'])->name('gofood.store');
Route::delete('/api/gofood/{id}', [GoFoodController::class, 'destroy'])->name('gofood.destroy');
Route::get('/gofood/{id}/edit', [GoFoodController::class, 'edit']);
Route::get('/gofood/{id}/edit-json', [GoFoodController::class, 'editJson']);
Route::put('/gofood/update/{id}', [GoFoodController::class, 'update'])->name('gofood.update');

// Route Grabfood
Route::get('/grabfood', [GrabfoodController::class, 'index'])->name('grabfood.index');
Route::get('/api/grabfood', [GrabfoodController::class, 'getAll']);
Route::post('/grabfood', [GrabfoodController::class, 'store'])->name('grabfood.store');
Route::delete('/api/grabfood/{id}', [GrabfoodController::class, 'destroy'])->name('grabfood.destroy');
Route::get('/grabfood/{id}/edit', [GrabfoodController::class, 'edit']);
Route::get('/grabfood/{id}/edit-json', [GrabfoodController::class, 'editJson']);
Route::put('/grabfood/update/{id}', [GrabfoodController::class, 'update'])->name('grabfood.update');

//Route Shopeefood
Route::get('/shopeefood', [ShopeefoodController::class, 'index'])->name('shopeefood.index');
Route::get('/api/shopeefood', [ShopeefoodController::class, 'getAll']);
Route::post('/shopeefood', [ShopeefoodController::class, 'store'])->name('shopeefood.store');
Route::delete('/api/shopeefood/{id}', [ShopeefoodController::class, 'destroy'])->name('shopeefood.destroy');
Route::get('/shopeefood/{id}/edit', [ShopeefoodController::class, 'edit']);
Route::get('/shopeefood/{id}/edit-json', [ShopeefoodController::class, 'editJson']);
Route::put('/shopeefood/update/{id}', [ShopeefoodController::class, 'update'])->name('shopeefood.update');

//Route item terjual
Route::prefix('items-terjual')->name('items-terjual.')->group(function () {
    Route::get('/gofood', [ItemTerjualController::class, 'gofood'])->name('gofood');
    Route::get('/grabfood', [ItemTerjualController::class, 'grabfood'])->name('grabfood');
    Route::get('/shopeefood', [ItemTerjualController::class, 'shopeefood'])->name('shopeefood');
});


// Route Laporan
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/download/excel', [DownloadController::class, 'downloadExcel'])->name('laporan.download.excel');
Route::get('/laporan/download/pdf', [DownloadController::class, 'downloadPdf'])->name('laporan.download.pdf');

require __DIR__.'/auth.php';
