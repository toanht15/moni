<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StaticHtmlImageSliderImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_html_image_slider_images', function(Blueprint $table)
		{
			$table->increments('id');
            $table->unsignedInteger('static_html_image_slider_id');
            $table->bigInteger('no');
            $table->string('image_url', 255)->default('');
            $table->string('caption', 40)->default('');
            $table->string('link')->default('');
            $table->index('static_html_image_slider_id', 'static_html_image_slider_id');
            $table->foreign('static_html_image_slider_id', 'static_html_image_slider_id_relation')->references('id')->on('static_html_image_sliders');
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
		Schema::drop('static_html_image_slider_images');
	}

}
