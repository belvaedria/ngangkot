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
        Schema::create('trayeks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_trayek'); 
            $table->string('nama_trayek'); 
            
            // Koordinat Titik Asal & Tujuan
            $table->double('lat_asal'); 
            $table->double('lng_asal');
            $table->double('lat_tujuan');
            $table->double('lng_tujuan');
            
            // Poin Penilaian 11: Materi Luar (Leaflet GeoJSON)
            $table->longText('rute_json')->nullable();
            
            $table->json('daftar_jalan')->nullable();
            $table->string('gambar_url')->nullable();

            // Logika Tampilan
            $table->string('kode_balik')->nullable(); // Relasi ke trayek arah sebaliknya
            $table->boolean('tampil_di_menu')->default(true); // Biar menu gak penuh
            
            $table->integer('harga');
            $table->string('warna_angkot')->default('#000000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trayeks');
    }
};
