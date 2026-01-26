<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodEntry;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FoodEntryController extends Controller
{
    // 1. Ambil data makanan HARI INI saja
    public function index()
    {
        $today = Carbon::today()->toDateString();
        return FoodEntry::where('date', $today)->get();
    }

    // 2. Simpan makanan baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required',
            'calories' => 'required|integer',
            'protein' => 'required|integer',
        ]);

        // Simpan ke database
        $entry = FoodEntry::create([
            'name' => $request->name,
            'calories' => $request->calories,
            'protein' => $request->protein,
            'date' => Carbon::today()->toDateString() // Otomatis tanggal hari ini
        ]);

        return response()->json($entry);
    }

    // 3. Hapus makanan
    public function destroy($id)
    {
        FoodEntry::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}