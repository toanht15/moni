<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableStaticHtmlExternalPageLoginType extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_html_external_page_login_types', function(Blueprint $table)
		{
            $table->increments('id');
            $table->unsignedInteger('static_html_entry_id');
            $table->foreign('static_html_entry_id','entry_id')->references('id')->on('static_html_entries');
            $table->tinyInteger('social_media_id');
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
        Schema::drop('static_html_external_page_login_types');
	}
}
