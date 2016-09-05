<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSliderImageCount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('static_html_image_sliders', function(Blueprint $table)
		{
            $table->tinyInteger('slider_pc_image_count')->default(0)->after('view_type');
            $table->tinyInteger('slider_sp_image_count')->default(0)->after('slider_pc_image_count');
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
            $table->dropColumn('slider_pc_image_count');
            $table->dropColumn('slider_sp_image_count');
		});
	}

}
