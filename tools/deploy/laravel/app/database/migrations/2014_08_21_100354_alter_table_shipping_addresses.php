<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableShippingAddresses extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('shipping_addresses');

        Schema::create('shipping_addresses', function(Blueprint $t)
        {
            $t->increments('id');
            $t->bigInteger('user_id')->unsigned();
            $t->string('first_name',45)->default('');
            $t->string('last_name',45)->default('');
            $t->string('first_name_kana',45)->default('');
            $t->string('last_name_kana',45)->default('');
            $t->string('zip_code1',3)->default('');
            $t->string('zip_code2',4)->default('');
            $t->integer('pref_id')->unsigned()->default(0);
            $t->string('address1',255)->default('');
            $t->string('address2',255)->default('');
            $t->string('address3',255)->default('');
            $t->string('tel_no1',4)->default('');
            $t->string('tel_no2',4)->default('');
            $t->string('tel_no3',4)->default('');
            $t->tinyInteger('del_flg')->default(0);
            
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_addresses');

        Schema::create('shipping_addresses', function(Blueprint $t)
        {
            $t->increments('id');
            $t->bigInteger('user_id')->unsigned();
            $t->string('first_name',45);
            $t->string('last_name',45);
            $t->string('first_name_kana',45);
            $t->string('last_name_kana',45);
            $t->string('zip_code1',3);
            $t->string('zip_code2',4);
            $t->integer('pref_id')->unsigned();
            $t->string('address1',255);
            $t->string('address2',255);
            $t->string('address3',255);
            $t->string('tel_no1',4);
            $t->string('tel_no2',4);
            $t->string('tel_no3',4);
            $t->tinyInteger('del_flg')->default(0);
            
            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->foreign('user_id')->references('id')->on('users');
            $t->foreign('pref_id')->references('id')->on('prefectures');
        });
    }

}
