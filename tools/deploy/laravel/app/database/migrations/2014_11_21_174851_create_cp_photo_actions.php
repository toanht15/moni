<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpPhotoActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('cp_photo_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cp_action_id');
            $table->string('title', 255)->default('');
            $table->string('image_url', 511)->default('');
            $table->string('text', 255)->default('');
            $table->string('button_label_text')->default('送信');
            $table->boolean('del_flg')->default(0);
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique('cp_action_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('cp_photo_actions');
    }

}
