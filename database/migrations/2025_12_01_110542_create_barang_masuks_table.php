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
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('barang_id');
            $table->foreign('barang_id')
                ->references('id')->on('barangs')
                ->onDelete('cascade');

            $table->integer('jumlah');

            // Kolom enum untuk jenis barang masuk
            $table->enum('jenis', ['pembelian', 'pengembalian_barang']);

            // Kolom tanggal masuk
            $table->date('tanggal_masuk')->nullable();

            // Tambahkan kolom keterangan
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
