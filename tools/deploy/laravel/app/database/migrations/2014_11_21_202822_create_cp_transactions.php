<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpTransactions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cp_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
        });
    }
}