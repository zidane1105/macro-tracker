<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('food_entries', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('calories');
        // TAMBAHKAN BARIS INI 👇
        $table->integer('protein')->default(0); 
        // ---------------------
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_entries');
    }
};
