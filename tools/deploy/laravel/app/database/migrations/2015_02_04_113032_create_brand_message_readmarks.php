<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandMessageReadmarks extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_message_readmarks', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('brand_notification_id')->unsigned();
            $table->integer('brand_id')->unsigned();
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('brand_notification_id')->references('id')->on('brand_notifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('brand_message_readmarks');
    }

}
