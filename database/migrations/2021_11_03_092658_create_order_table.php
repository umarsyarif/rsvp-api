<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('jumlah_orang')->unsigned();
            $table->time('jam');
            $table->date('tanggal');
            $table->integer('subtotal')->unsigned()->nullable();
            $table->integer('diskon')->unsigned()->nullable();
            $table->integer('total')->unsigned()->nullable();
            $table->enum('tipe', ['dine in', 'take away']);
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
        Schema::dropIfExists('order');
    }
}
