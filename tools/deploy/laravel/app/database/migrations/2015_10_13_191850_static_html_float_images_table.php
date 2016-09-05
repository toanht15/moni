<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StaticHtmlFloatImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_html_float_images', function(Blueprint $table)
		{
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->integer('position_type');
            $table->tinyInteger('smartphone_float_off_flg')->default(0);
            $table->string('image_url', 255)->default('');
            $table->string('caption', 40)->default('');
            $table->text('text')->default('');
            $table->string('link')->default('');
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
		Schema::drop('static_html_float_images');
	}

}
