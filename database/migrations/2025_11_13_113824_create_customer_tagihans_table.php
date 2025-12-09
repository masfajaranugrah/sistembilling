<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_tagihans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke users (UUID)
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Relasi ke tagihans (UUID juga, sesuaikan jika tagihans.id = uuid)
            $table->uuid('tagihan_id');
            $table->foreign('tagihan_id')->references('id')->on('tagihans')->onDelete('cascade');

            $table->string('status_pembayaran')->default('belum bayar');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->date('tanggal_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->string('kwitansi')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_tagihans');
    }
};
