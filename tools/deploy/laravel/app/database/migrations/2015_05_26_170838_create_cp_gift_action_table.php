<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpGiftActionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_gift_actions', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('title', 255)->default('');
            $table->string('image_url', 512)->default('');
            $table->longText('text')->default('');
            $table->longText('html_content')->default('');
            $table->longText('receiver_text')->default('');
            $table->string('button_label_text')->default('');
            $table->tinyInteger('card_required')->default(1);
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('cp_action_id');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_gift_actions');
	}

}
