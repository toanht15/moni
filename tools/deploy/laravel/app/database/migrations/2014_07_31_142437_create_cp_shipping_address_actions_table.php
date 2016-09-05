<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpShippingAddressActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('cp_shipping_address_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->longText('text');
            $table->tinyInteger('name_required')->default(1);
            $table->tinyInteger('address_required')->default(1);
            $table->tinyInteger('tel_required')->default(1);
            $table->string('button_label_text')->default('送信する');
            $table->tinyInteger('del_flg')->default(0);
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
	public function down()
	{
		Schema::drop('cp_shipping_address_actions');
	}

}
