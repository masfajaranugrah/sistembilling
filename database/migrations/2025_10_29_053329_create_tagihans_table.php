<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('pelanggan_id')
                ->constrained('pelanggans')
                ->onDelete('cascade');

            $table->foreignUuid('paket_id')
                ->nullable()
                ->constrained('pakets')
                ->onDelete('set null');

            $table->string('nama_paket')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->string('kecepatan')->nullable();
            $table->integer('masa_pembayaran')->nullable();

            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');

            $table->enum('status_pembayaran', ['belum bayar', 'proses_verifikasi', 'lunas'])
                ->default('belum bayar');

            $table->foreignUuid('type_pembayaran')
                ->nullable()
                ->constrained('rekenings')
                ->onDelete('set null');

            $table->date('tanggal_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->string('kwitansi')->nullable();

            $table->timestamps();

            $table->index(['pelanggan_id', 'paket_id', 'status_pembayaran']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
