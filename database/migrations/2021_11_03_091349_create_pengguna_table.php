<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenggunaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('username', 255);
            $table->string('password', 100);
            $table->enum('role', ['admin', 'pelanggan']);
            $table->string('email', 255);
            $table->string('alamat', 255);
            $table->string('no_hp', 12);
            $table->tinyInteger('is_verified')->unsigned();
            $table->integer('poin')->unsigned();
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
        Schema::dropIfExists('pengguna');
    }
}
