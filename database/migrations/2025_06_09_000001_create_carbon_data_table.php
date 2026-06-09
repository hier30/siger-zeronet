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
        Schema::create('carbon_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')
      ->constrained('kecamatan')
      ->onDelete('cascade');
            $table->integer('tahun');
            $table->decimal('emisi_co2', 12, 2)->default(0);
            $table->decimal('absorpsi_co2', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['kecamatan_id', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carbon_data');
    }
};
