<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotoUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('photo_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cp_action_id');
            $table->unsignedInteger('cp_user_id');
            $table->string('photo_url', 511)->default('');
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('cp_user_id')->references('id')->on('cp_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('photo_users');
    }

}
