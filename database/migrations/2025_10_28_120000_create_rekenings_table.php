<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekeningsTable extends Migration
{
    public function up()
    {
        Schema::create('rekenings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_bank');
            $table->string('nomor_rekening')->unique();
            $table->string('nama_pemilik');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekenings');
    }
}
