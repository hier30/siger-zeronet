<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sertifikat_karbons', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemilik');
            $table->string('wilayah');
            $table->decimal('luas_lahan', 12, 2);
            $table->decimal('jumlah_serapan', 12, 2);
            $table->string('satuan')->default('ton CO2e');
            $table->integer('tahun');
            $table->enum('status', ['Aktif', 'Menunggu Verifikasi', 'Tidak Aktif']);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tahun', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sertifikat_karbons');
    }
};
