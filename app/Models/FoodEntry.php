<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodEntry extends Model
{
    use HasFactory;

    // INI KUNCINYA: Memberi izin kolom mana yang boleh diisi
    protected $fillable = [
        'name',
        'calories',
        'protein'
    ];
}