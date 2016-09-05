<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdsAudiencesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('ads_audiences', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('brand_user_relation_id')->unsigned();
            $table->string('name');
            $table->string('description');
            $table->text('search_condition');
            $table->tinyInteger('search_type')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('brand_user_relation_id')->references('id')->on('brands_users_relations');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('ads_audiences');
	}
}
