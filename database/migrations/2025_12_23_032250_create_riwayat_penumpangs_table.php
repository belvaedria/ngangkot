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
        Schema::create('riwayat_penumpangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Penumpang
            $table->string('asal_nama'); // Nama lokasi awal (misal: "Dago")
            $table->string('tujuan_nama'); // Nama tujuan (misal: "Dipatiukur")
            $table->string('asal_coords'); // Koordinat "lat,long"
            $table->string('tujuan_coords'); // Koordinat "lat,long"
            $table->longText('rute_hasil_json'); // JSON rute yang disarankan sistem (snapshot)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_penumpangs');
    }
};
