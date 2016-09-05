<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftCardConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gift_card_configs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_gift_action_id')->unsigned();
            $table->integer('card_num')->default(1);
            $table->tinyInteger('required')->default(1);
            $table->integer('width')->default(580);
            $table->integer('height')->default(348);
            $table->string('text_color', 20)->default('#111111');

            $table->integer('to_x')->default(20);
            $table->integer('to_y')->default(20);
            $table->integer('to_text_size')->default(13);
            $table->integer('to_size')->default(160);

            $table->integer('from_x')->default(390);
            $table->integer('from_y')->default(290);
            $table->integer('from_text_size')->default(13);
            $table->integer('from_size')->default(160);

            $table->integer('content_x')->default(20);
            $table->integer('content_y')->default(70);
            $table->integer('content_width')->default(530);
            $table->integer('content_height')->default(200);
            $table->integer('content_text_size')->default(20);
            $table->longText('content_default_text')->default('');

            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->foreign('cp_gift_action_id')->references('id')->on('cp_gift_actions');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gift_card_configs');
	}

}
