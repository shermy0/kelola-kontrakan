<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sewa', function (Blueprint $table) {
            $table->enum('status_sewa', ['menunggu', 'aktif', 'selesai', 'ditolak'])
                  ->default('menunggu')
                  ->change();

            $table->date('tgl_mulai')->nullable()->change();
            $table->date('tgl_selesai')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sewa', function (Blueprint $table) {
            $table->enum('status_sewa', ['aktif', 'selesai'])
                  ->default('aktif')
                  ->change();

            $table->date('tgl_mulai')->nullable(false)->change();
            $table->date('tgl_selesai')->nullable(false)->change();
        });
    }
};
