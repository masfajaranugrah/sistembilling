<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('barang_id');

            $table->foreign('barang_id')
                ->references('id')->on('barangs')
                ->onDelete('cascade');

            $table->integer('jumlah');
            $table->string('diambil_oleh'); // Nama/Orang yang mengambil barang
            $table->text('keterangan')->nullable();
            $table->date('tanggal'); // ➕ Tanggal barang keluar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};
