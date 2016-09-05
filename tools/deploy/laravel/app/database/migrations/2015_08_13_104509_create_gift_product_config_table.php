<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftProductConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gift_product_configs', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('cp_gift_action_id')->unsigned();
            $table->longText('product_text')->default('');
            $table->longText('product_html_content')->default('');
            $table->tinyInteger('postal_name_flg')->default(1);
            $table->tinyInteger('postal_address_flg')->default(1);
            $table->tinyInteger('postal_tel_flg')->default(1);
            $table->dateTime('expire_datetime')->default('0000-00-00 00:00:00');
            $table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

            $table->index('cp_gift_action_id');
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
		Schema::drop('gift_product_configs');
	}

}
