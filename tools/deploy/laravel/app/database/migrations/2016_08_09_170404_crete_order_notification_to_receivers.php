<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreteOrderNotificationToReceivers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_notification_to_receivers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->unsignedInteger('product_id');
			$table->string('mail_address');
			$table->boolean('del_flg')->default(0);;
			$table->timestamps();

			$table->index('mail_address');
			$table->foreign('product_id')->references('id')->on('products');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('order_notification_to_receivers');
	}

}
