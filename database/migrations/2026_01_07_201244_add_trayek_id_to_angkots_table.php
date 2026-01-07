<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('angkots', function (Blueprint $table) {
            $table->foreignId('trayek_id')->after('plat_nomor')->constrained('trayeks')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('angkots', function (Blueprint $table) {
            $table->dropForeign(['trayek_id']);
            $table->dropColumn('trayek_id');
        });
    }
};
