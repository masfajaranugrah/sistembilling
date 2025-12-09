<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // ????? Data Identitas
            $table->string('nama_lengkap');
            $table->string('no_ktp')->nullable();
            $table->string('no_whatsapp')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('webpushr_sid')->nullable();

            // ?? Data Alamat Lengkap
            $table->string('alamat_jalan')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();

            // ?? Paket Internet
            $table->foreignUuid('paket_id')->constrained('pakets')->onDelete('cascade');
            $table->string('nomer_id')->unique();

            // ?? Tanggal Langganan
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();

            // ?? Lain-lain
            $table->text('deskripsi')->nullable();
            $table->string('foto_ktp')->nullable();

            // ?? Status Pelanggan
            $table->enum('status', ['pending', 'approve', 'reject'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
