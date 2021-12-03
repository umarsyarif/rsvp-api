<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('nama', 255);
            $table->string('foto', 255);
            $table->integer('harga')->unsigned();
            $table->integer('diskon')->unsigned();
            $table->integer('id_satuan')->unsigned();
            $table->enum('tipe', ['makanan', 'minuman']);
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
        Schema::dropIfExists('menu');
    }
}
