<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontrakan', function (Blueprint $table) {
            $table->id('id_kontrakan');
            $table->string('nomor_unit')->unique();
            $table->decimal('harga_sewa', 12, 2);
            $table->enum('status', ['kosong', 'terisi'])->default('kosong');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontrakan');
    }
};
