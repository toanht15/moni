<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStaticHtmlInstagrams extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_html_instagrams', function(Blueprint $table)
		{
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->text('api_url')->default('');
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
        Schema::drop('static_html_instagrams');
	}
}
