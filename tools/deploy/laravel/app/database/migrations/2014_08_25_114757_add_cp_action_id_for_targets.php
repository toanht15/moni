<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCpActionIdForTargets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

        Schema::drop('cp_message_delivery_targets');

        Schema::create('cp_message_delivery_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_message_delivery_reservation_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->dateTime('deliveredDate')->default('0000-00-00 00:00:00');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_message_delivery_reservation_id', 'cp_message_delivery_reservation_id_user_id_unique')->references('id')->on('cp_message_delivery_reservations');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique(array('cp_message_delivery_reservation_id', 'user_id'), 'cp_message_delivery_reservation_id_user_id_unique');
            $table->unique(array('user_id', 'cp_action_id'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

        Schema::drop('cp_message_delivery_targets');

        Schema::create('cp_message_delivery_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cp_message_delivery_reservation_id')->unsigned();
            $table->dateTime('deliveredDate')->default('0000-00-00 00:00:00');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_message_delivery_reservation_id', 'cp_message_delivery_reservation_id_user_id_unique')->references('id')->on('cp_message_delivery_reservations');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('cp_message_delivery_reservation_id', 'user_id'), 'cp_message_delivery_reservation_id_user_id_unique');
        });
	}

}
