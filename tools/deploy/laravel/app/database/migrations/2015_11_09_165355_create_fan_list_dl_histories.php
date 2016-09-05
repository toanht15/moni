<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFanListDlHistories extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fan_list_dl_histories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('brand_id');
            $table->text('search_condition');
            $table->text('files');
            $table->tinyInteger('downloaded')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('brand_id')->references('id')->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('fan_list_dl_histories');
    }

}
