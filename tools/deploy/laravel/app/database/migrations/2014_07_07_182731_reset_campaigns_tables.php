<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResetCampaignsTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::drop('user_campaign_action_statuses');
        Schema::drop('user_campaign_joined_statuses');
        Schema::drop('user_campaign_messages');
        Schema::drop('user_campaign_threads');

        Schema::drop('message_delivery_targets');
        Schema::drop('message_delivery_reservations');

        Schema::drop('entry_actions');
        Schema::drop('message_actions');

        Schema::drop('campaign_public_reservations');
        Schema::drop('campaign_next_actions');
        Schema::drop('campaign_actions');
        Schema::drop('campaign_action_groups');
        Schema::drop('campaigns');

        Schema::create('cps', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->string('title', 60)->default('');
            $table->dateTime('publish_date')->default('0000-00-00 00:00:00');
            $table->dateTime('start_date')->default('0000-00-00 00:00:00');
            $table->dateTime('end_date')->default('0000-00-00 00:00:00');
            $table->dateTime('announce_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('selection_method')->default(0);
            $table->tinyInteger('shipping_method')->default(0);
            $table->tinyInteger('winner_count')->default(0);
            $table->string('image_url')->default(0);
            $table->tinyInteger('show_monipla_com_flg')->default(0);
            $table->tinyInteger('show_top_page_flg')->default(0);
            $table->tinyInteger('show_navigation_flg')->default(0);
            $table->tinyInteger('get_address_type')->default(0);
            $table->tinyInteger('fix_basic_flg')->default(0);
            $table->tinyInteger('fix_attract_flg')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('close_flg')->default(0);
            $table->tinyInteger('public_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('cp_action_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->tinyInteger('order_no')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_id')->references('id')->on('cps');
        });


        Schema::create('cp_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_group_id')->unsigned();
            $table->tinyInteger('order_no')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_group_id')->references('id')->on('cp_action_groups');

        });

        Schema::create('cp_next_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->integer('cp_next_action_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('cp_next_action_id')->references('id')->on('cp_actions');
        });

        Schema::create('cp_public_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->dateTime('public_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_id')->references('id')->on('cps');
            $table->unique('cp_id');

        });


        Schema::create('cp_entry_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->longText('text');
            $table->string('button_label_text')->default('応募する');
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique('cp_action_id');
            $table->timestamps();
        });

        Schema::create('cp_message_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->longText('text');
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique('cp_action_id');
            $table->timestamps();
        });

        Schema::create('cp_message_delivery_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->dateTime('deliveryDate')->default('0000-00-00 00:00:00');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

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

        Schema::create('cp_user_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('title');
            $table->tinyInteger('total_message_count')->default(0);
            $table->tinyInteger('unread_message_count')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_id')->references('id')->on('cps');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('cp_id', 'user_id'));
        });


        Schema::create('cp_user_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cp_user_thread_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->longText('contents');
            $table->tinyInteger('read_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_user_thread_id')->references('id')->on('cp_user_threads');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique(array('cp_user_thread_id', 'cp_action_id'));
        });


        Schema::create('cp_user_action_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('cp_action_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->unique(array('user_id', 'cp_action_id'));
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::drop('cp_user_action_statuses');
        Schema::drop('cp_user_messages');
        Schema::drop('cp_user_threads');

        Schema::drop('cp_message_delivery_targets');
        Schema::drop('cp_message_delivery_reservations');

        Schema::drop('cp_entry_actions');
        Schema::drop('cp_message_actions');

        Schema::drop('cp_public_reservations');
        Schema::drop('cp_next_actions');
        Schema::drop('cp_actions');
        Schema::drop('cp_action_groups');
        Schema::drop('cps');


        Schema::create('campaigns', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->string('title', 60)->default('');
            $table->dateTime('publish_date')->default('0000-00-00 00:00:00');
            $table->dateTime('start_date')->default('0000-00-00 00:00:00');
            $table->dateTime('end_date')->default('0000-00-00 00:00:00');
            $table->dateTime('announce_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('selection_method')->default(0);
            $table->tinyInteger('shipping_method')->default(0);
            $table->tinyInteger('winner_count')->default(0);
            $table->string('image_url')->default(0);
            $table->tinyInteger('show_monipla_com_flg')->default(0);
            $table->tinyInteger('show_top_page_flg')->default(0);
            $table->tinyInteger('show_navigation_flg')->default(0);
            $table->tinyInteger('get_address_type')->default(0);
            $table->tinyInteger('fix_basic_flg')->default(0);
            $table->tinyInteger('fix_attract_flg')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('close_flg')->default(0);
            $table->tinyInteger('public_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_id')->references('id')->on('brands');
        });

        Schema::create('campaign_action_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id')->unsigned();
            $table->tinyInteger('order_no')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
        });


        Schema::create('campaign_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_action_group_id')->unsigned();
            $table->tinyInteger('order_no')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('campaign_action_group_id')->references('id')->on('campaign_action_groups');

        });

        Schema::create('campaign_next_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_action_id')->unsigned();
            $table->integer('campaign_next_action_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->foreign('campaign_next_action_id')->references('id')->on('campaign_actions');
        });

        Schema::create('campaign_public_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_id')->unsigned();
            $table->dateTime('public_date')->default('0000-00-00 00:00:00');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->unique('campaign_id');

        });


        Schema::create('entry_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->longText('text');
            $table->string('button_label_text')->default('応募する');
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique('campaign_action_id');
            $table->timestamps();
        });

        Schema::create('message_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('campaign_action_id')->unsigned();
            $table->string('image_url', 512)->default('');
            $table->longText('text');
            $table->tinyInteger('del_flg')->default(0);
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique('campaign_action_id');
            $table->timestamps();
        });


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

        Schema::create('message_delivery_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('message_delivery_reservation_id')->unsigned();
            $table->dateTime('deliveredDate')->default('0000-00-00 00:00:00');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('message_delivery_reservation_id', 'message_delivery_reservation_id_user_id_unique')->references('id')->on('message_delivery_reservations');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(array('message_delivery_reservation_id', 'user_id'), 'message_delivery_reservation_id_user_id_unique');
        });


        Schema::create('user_campaign_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            $table->string('title');
            $table->tinyInteger('total_message_count')->default(0);
            $table->tinyInteger('unread_message_count')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->unique(array('user_id', 'campaign_id'));
        });

        Schema::create('user_campaign_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_campaign_thread_id')->unsigned();
            $table->integer('campaign_action_id')->unsigned();
            $table->longText('contents');
            $table->tinyInteger('read_flg')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_campaign_thread_id')->references('id')->on('user_campaign_threads');
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique(array('user_campaign_thread_id', 'campaign_action_id'), 'user_campaign_thread_id_campaign_action_id_unique');
        });

        Schema::create('user_campaign_joined_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->unique(array('user_id', 'campaign_id'));
        });

        Schema::create('user_campaign_action_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('campaign_action_id')->unsigned();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('campaign_action_id')->references('id')->on('campaign_actions');
            $table->unique(array('user_id', 'campaign_action_id'));
        });
    }

}
