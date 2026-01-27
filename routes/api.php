<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\FoodEntry;

// 1. AMBIL DATA (GET)
Route::get('/food-entries', function () {
    return FoodEntry::latest()->get();
});

// 2. SIMPAN DATA (POST)
Route::post('/food-entries', function (Request $request) {
    $request->validate([
        'name' => 'required',
        'calories' => 'required|integer',
        'protein' => 'nullable|integer'
    ]);

    return FoodEntry::create([
        'name' => $request->name,
        'calories' => $request->calories,
        'protein' => $request->protein ?? 0
    ]);
});

// 3. HAPUS DATA (DELETE) - [BARU]
Route::delete('/food-entries/{id}', function ($id) {
    FoodEntry::destroy($id); // Hapus data berdasarkan ID
    return response()->json(['message' => 'Berhasil dihapus']);
});