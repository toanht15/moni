<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileQuestionnaire extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('profile_questionnaires', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->string('question', 2048)->default('');
            $table->tinyInteger('requirement_flg')->default(1);
            $table->tinyInteger('type')->default(1);
            $table->string('choices', 255)->default('');
            $table->tinyInteger('public_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('profile_questionnaires');
	}

}
