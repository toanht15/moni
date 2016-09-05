<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftMessageTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gift_messages', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('cp_user_id')->unsigned();
            $table->integer('cp_gift_action_id')->unsigned();
            $table->string('param_hash', 255)->default('');
            $table->string('image_url', 255)->default('');
            $table->bigInteger('coupon_code_id')->unsigned();
            $table->tinyInteger('send_flg')->default(0);
            $table->tinyInteger('media_type')->default(0);
            $table->bigInteger('receiver_user_id')->default(0);
            $table->string('sender_text', 255)->default('');
            $table->string('receiver_text', 255)->default('');
            $table->longText('content_text')->default('');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->index('cp_user_id');
            $table->index('param_hash');

            $table->foreign('cp_user_id')->references('id')->on('cp_users');
            $table->foreign('cp_gift_action_id')->references('id')->on('cp_gift_actions');
            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gift_messages');
	}

}
