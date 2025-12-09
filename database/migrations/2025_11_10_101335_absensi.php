<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke users pakai UUID
            $table->uuid('user_id');
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->date('date'); // tanggal absensi (Y-m-d)

            // --- Data Masuk ---
            $table->timestamp('time_in')->nullable();
            $table->string('photo_in')->nullable(); // path foto masuk
            $table->decimal('lat_in', 10, 7)->nullable(); // latitude masuk
            $table->decimal('lng_in', 10, 7)->nullable(); // longitude masuk

            // --- Data Pulang ---
            $table->timestamp('time_out')->nullable();
            $table->string('photo_out')->nullable(); // path foto pulang
            $table->decimal('lat_out', 10, 7)->nullable(); // latitude pulang
            $table->decimal('lng_out', 10, 7)->nullable(); // longitude pulang

            // --- Data Lembur ---
            $table->timestamp('lembur_in')->nullable();
            $table->timestamp('lembur_out')->nullable();
            $table->decimal('lat_lembur_in', 10, 7)->nullable();
            $table->decimal('lng_lembur_in', 10, 7)->nullable();
            $table->decimal('lat_lembur_out', 10, 7)->nullable();
            $table->decimal('lng_lembur_out', 10, 7)->nullable();
            $table->string('photo_lembur_in')->nullable();
            $table->string('photo_lembur_out')->nullable();

            // --- Perhitungan Waktu ---
            $table->decimal('total_hours', 5, 2)->nullable(); // total jam kerja
            $table->decimal('overtime_hours', 5, 2)->nullable(); // jam lembur

            $table->text('note')->nullable();
            $table->timestamps();

            // Satu record per user per tanggal
            $table->unique(['user_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }
};
