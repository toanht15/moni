<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpPaymentActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cp_payment_actions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('title');
			$table->integer('product_id')->unsigned();
			$table->integer('cp_action_id')->unsigned();
			$table->string('image_url', 512)->default('');
			$table->longText('text');
			$table->text('html_content');
			$table->longText('finish_text');
			$table->text('finish_html_content');
			$table->tinyInteger('del_flg')->default(0);
			$table->timestamps();

			$table->foreign('product_id')->references('id')->on('products');
			$table->foreign('cp_action_id')->references('id')->on('cp_actions');
			$table->unique('cp_action_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cp_payment_actions');
	}

}
