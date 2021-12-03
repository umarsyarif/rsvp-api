<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRiwayatPoinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('riwayat_poin', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('id_pengguna')->unsigned();
            $table->integer('nominal')->unsigned();
            $table->enum('tipe', ['plus', 'minus']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riwayat_poin');
    }
}
