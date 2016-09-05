<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBrandNotifications extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_notifications', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('subject',255);
            $table->longText('contents');
            $table->date('publish_at')->default('0000-00-00');
            $table->tinyInteger('message_type')->default(0);
            $table->string('author',255);
            $table->tinyInteger('del_flg')->default(0);
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
        Schema::drop('brand_notifications');
    }

}
