<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserAttributes extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_attributes', function(Blueprint $t)
        {
            $t->increments('id');
            $t->bigInteger('user_id')->unsigned();
            $t->integer('user_attribute_master_id');
            $t->string('value',30);
            $t->tinyInteger('del_flg')->default(0);

            // created_at, updated_at DATETIME
            $t->timestamps();
            $t->foreign('user_id')->references('id')->on('users');
            $t->foreign('user_attribute_master_id')->references('id')->on('user_attribute_masters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_attributes');
    }

}
