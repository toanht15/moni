<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpAnnounceDeliveryActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cp_announce_delivery_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cp_action_id');
            $table->string('title', 255)->default('');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('cp_announce_delivery_actions');
    }

}
