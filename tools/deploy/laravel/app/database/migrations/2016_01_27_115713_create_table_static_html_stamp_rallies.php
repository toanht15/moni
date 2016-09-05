<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStaticHtmlStampRallies extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('static_html_stamp_rallies', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('template_id');
            $table->integer('campaign_count')->default(0);
            $table->string('stamp_status_joined_image')->default('');
            $table->string('stamp_status_finished_image')->default('');
            $table->string('stamp_status_coming_soon_image')->default('');
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
        Schema::drop('static_html_stamp_rallies');
	}
}
