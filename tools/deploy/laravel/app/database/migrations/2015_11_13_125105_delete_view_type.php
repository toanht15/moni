<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteViewType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_image_sliders', function(Blueprint $table)
		{
            $table->dropColumn("view_type");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('static_html_image_sliders', function(Blueprint $table)
		{
            $table->integer('view_type')->after("tempalte_id");
		});
	}

}
