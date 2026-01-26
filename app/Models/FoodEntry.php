<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodEntry extends Model
{
    // Ini daftar kolom yang BOLEH diisi
    protected $fillable = ['name', 'calories', 'protein', 'date'];
}