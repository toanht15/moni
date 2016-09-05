<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCampaignActionMessageDeliveries extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('message_delivery_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_action_id')->unsigned();
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->dateTime('deliveryDate')->default('0000-00-00 00:00:00');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
        });

        Schema::create('message_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('message_delivery_reservation_id')->unsigned();
            $table->dateTime('deliveryDate')->default('0000-00-00 00:00:00');
            $table->dateTime('deliveredDate')->default('0000-00-00 00:00:00');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('message_delivery_reservation_id', 'message_delivery_reservation_id_user_id_unique')->references('id')->on('message_delivery_reservations');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('message_delivery_reservation_id', 'user_id'), 'message_delivery_reservation_id_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('message_deliveries');
        Schema::drop('message_delivery_reservations');
    }

}
