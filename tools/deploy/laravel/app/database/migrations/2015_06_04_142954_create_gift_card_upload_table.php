<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftCardUploadTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gift_card_uploads', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('gift_card_config_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index('gift_card_config_id');
            $table->foreign('gift_card_config_id')->references('id')->on('gift_card_configs');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gift_card_uploads');
	}

}
