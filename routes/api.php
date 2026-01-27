<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\FoodEntry;

// 1. Jalur untuk MENGAMBIL data (GET)
Route::get('/food-entries', function () {
    return FoodEntry::latest()->get();
});

// 2. Jalur untuk MENYIMPAN data (POST) - INI YANG KEMARIN KURANG
Route::post('/food-entries', function (Request $request) {
    // Validasi input (Biar aman)
    $request->validate([
        'name' => 'required',
        'calories' => 'required|integer',
        'protein' => 'nullable|integer'
    ]);

    // Simpan ke database
    return FoodEntry::create([
        'name' => $request->name,
        'calories' => $request->calories,
        'protein' => $request->protein ?? 0 // Kalau kosong, isi 0
    ]);
});