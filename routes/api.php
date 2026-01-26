<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FoodEntryController;

// Ini jalur untuk mengambil data makanan
Route::get('/foods', [FoodEntryController::class, 'index']);

// Ini jalur untuk menyimpan makanan baru
Route::post('/foods', [FoodEntryController::class, 'store']);

// Ini jalur untuk menghapus makanan
Route::delete('/foods/{id}', [FoodEntryController::class, 'destroy']);