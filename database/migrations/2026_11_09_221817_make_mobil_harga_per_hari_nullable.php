<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobil', function (Blueprint $table) {
            $table->unsignedBigInteger('harga_per_hari')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mobil', function (Blueprint $table) {
            $table->unsignedBigInteger('harga_per_hari')->nullable(false)->change();
        });
    }
};
