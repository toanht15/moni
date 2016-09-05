<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DropCpPublicReservations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::drop('cp_public_reservations');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::create('cp_public_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->dateTime('public_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_id')->references('id')->on('cps');
            $table->unique('cp_id');

        });
    }
}
