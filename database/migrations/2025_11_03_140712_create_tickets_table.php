<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('pelanggan_id')->nullable();
            $table->foreign('pelanggan_id')->references('id')->on('pelanggans')->onDelete('set null');

            $table->string('phone')->nullable();
            $table->string('location_link')->nullable();

            $table->string('category')->nullable();
            $table->text('issue_description');
            $table->text('additional_note')->nullable();

            $table->text('cs_note')->nullable();
            $table->string('attachment')->nullable();

            $table->text('technician_note')->nullable();
            $table->string('technician_attachment')->nullable();

            $table->enum('complaint_source', ['whatsapp', 'telepon', 'datang', 'email', 'app'])->default('whatsapp');

            $table->enum('priority', ['urgent', 'medium', 'low'])->default('medium');
            $table->unsignedBigInteger('technician_group_id')->nullable();

            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->enum('status', ['pending', 'assigned', 'progress', 'finished', 'approved', 'rejected'])->default('pending');

            // Kolom created_by YANG BENAR
            $table->uuid('created_by')->nullable();

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
