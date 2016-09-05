<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StaticHtmlFullImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_html_full_images', function(Blueprint $table)
		{
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->string('image_url', 255)->default('');
            $table->string('link')->default('');
            $table->string('caption', 40)->default('');
            $table->unique('template_id');
            $table->foreign('template_id')->references('id')->on('static_html_templates');
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
		Schema::drop('static_html_full_images');
	}

}
