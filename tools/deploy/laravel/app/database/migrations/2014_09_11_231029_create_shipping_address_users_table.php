<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShippingAddressUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        
        Schema::create('shipping_address_users', function($t) {
            // auto increment id (primary key)
            $t->increments('id');
            $t->integer('cp_user_id')->unsigned();
            $t->integer('cp_shipping_address_action_id')->unsigned();
            $t->string('first_name', 45)->default('');
            $t->string('last_name', 45)->default('');
            $t->string('first_name_kana', 45)->default('');
            $t->string('last_name_kana', 45)->default('');
            $t->string('zip_code1', 3)->default('');
            $t->string('zip_code2', 4)->default('');
            $t->tinyInteger('pref_id')->default(0);
            $t->string('address1', 255)->default('');
            $t->string('address2', 255)->default('');
            $t->string('address3', 255)->default('');
            $t->string('tel_no1', 4)->default('');
            $t->string('tel_no2', 4)->default('');
            $t->string('tel_no3', 4)->default('');
            $t->tinyInteger('del_flg')->default(0);
            
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->index('cp_shipping_address_action_id');
            $t->index('cp_user_id');
            $t->foreign('cp_shipping_address_action_id')->references('id')->on('cp_shipping_address_actions');
            $t->foreign('cp_user_id')->references('id')->on('cp_users');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('shipping_address_users');
	}

}
