<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		Schema::create('campaigns', function (Blueprint $t) {

			$t->increments('id');
			$t->integer('brand_id')->unsigned();
			$t->string('title', 60)->default('');
			$t->dateTime('start_date')->default('0000-00-00 00:00:00');
			$t->dateTime('end_date')->default('0000-00-00 00:00:00');
			$t->dateTime('announce_date')->default('0000-00-00 00:00:00');
			$t->tinyInteger('selection_method')->default(0);
			$t->tinyInteger('shipping_method')->default(0);
			$t->tinyInteger('winner_count')->default(0);
			$t->string('image_url')->default('');
			$t->tinyInteger('show_monipla_com_flg')->default(0);
			$t->tinyInteger('show_top_page_flg')->default(0);
			$t->tinyInteger('show_navigation_flg')->default(0);
			$t->tinyInteger('close_flg')->default(0);
			$t->tinyInteger('public_flg')->default(0);
			$t->timestamps();
			$t->foreign('brand_id')->references('id')->on('brands');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('campaigns');
	}

}
