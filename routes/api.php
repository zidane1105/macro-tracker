<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\FoodEntry;

//
// ================= CRUD DASAR =================
//

// 1. GET semua data
Route::get('/food-entries', function () {
    return FoodEntry::latest()->get();
});

// 2. GET satu data
Route::get('/food-entries/{id}', function ($id) {
    return FoodEntry::findOrFail($id);
});

// 3. POST tambah data
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

// 4. PUT update data
Route::put('/food-entries/{id}', function (Request $request, $id) {
    $food = FoodEntry::findOrFail($id);

    $request->validate([
        'name' => 'sometimes|required',
        'calories' => 'sometimes|required|integer',
        'protein' => 'nullable|integer'
    ]);

    $food->update($request->all());

    return response()->json([
        'message' => 'Berhasil diupdate',
        'data' => $food
    ]);
});

// 5. DELETE data
Route::delete('/food-entries/{id}', function ($id) {
    FoodEntry::destroy($id);
    return response()->json(['message' => 'Berhasil dihapus']);
});

//
// ================= ALGORITMA (WAJIB TUGAS) =================
//

// 6. Sorting manual berdasarkan kalori (Bubble Sort)
Route::get('/food-entries/sort/calories', function () {
    $foods = FoodEntry::all()->toArray();

    $n = count($foods);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($foods[$j]['calories'] < $foods[$j + 1]['calories']) {
                $temp = $foods[$j];
                $foods[$j] = $foods[$j + 1];
                $foods[$j + 1] = $temp;
            }
        }
    }

    return response()->json($foods);
});

// 7. Searching manual berdasarkan nama
Route::get('/food-entries/search/{name}', function ($name) {
    $foods = FoodEntry::all();
    $result = [];

    foreach ($foods as $food) {
        if (stripos($food->name, $name) !== false) {
            $result[] = $food;
        }
    }

    return response()->json($result);
});