<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewa', function (Blueprint $table) {
            $table->id('id_sewa');
            
            $table->unsignedBigInteger('id_penyewa');
            $table->unsignedBigInteger('id_kontrakan');
            
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->enum('status_sewa', ['aktif', 'selesai'])->default('aktif');
            
            $table->timestamps();

            // Relasi
            $table->foreign('id_penyewa')->references('id_penyewa')->on('penyewa')->onDelete('cascade');
            $table->foreign('id_kontrakan')->references('id_kontrakan')->on('kontrakan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewa');
    }
};
