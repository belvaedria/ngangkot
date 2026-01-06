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
        Schema::create('angkots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(); // Supir yang bawa
            $table->string('kode_trayek');
            $table->string('plat_nomor');
            
            // Tracking Posisi via API Driver (Poin 7: Async update lokasi nanti)
            $table->double('lat_sekarang')->nullable();
            $table->double('lng_sekarang')->nullable();
            $table->boolean('is_active')->default(false); 

            $table->foreign('kode_trayek')
                ->references('kode_trayek')
                ->on('trayeks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angkots');
    }
};
