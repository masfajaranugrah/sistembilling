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
        Schema::create('pakets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_paket');
            $table->decimal('harga', 12, 2);
            $table->integer('masa_pembayaran')->default(30); // hari
            $table->enum('cycle', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->integer('kecepatan')->default(10); // Mbps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakets');
    }
};
