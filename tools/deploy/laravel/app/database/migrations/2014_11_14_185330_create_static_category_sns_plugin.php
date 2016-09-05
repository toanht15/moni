<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticCategorySnsPlugin extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('static_html_category_sns_plugins', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
            $table->integer('sns_plugin_id')->unsigned()->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('static_html_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('static_html_category_sns_plugins');
    }
}
