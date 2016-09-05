<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StaticHtmlEntryToTemplateMappingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('static_html_entry_to_template_mappings', function(Blueprint $table)
		{
			$table->increments('id');
            $table->unsignedInteger('static_html_entry_id');
            $table->unsignedInteger('template_id');
            $table->bigInteger('no');
            $table->index('static_html_entry_id', 'static_html_entry_id_unique');
            $table->index('template_id');
            $table->foreign('static_html_entry_id', 'static_html_entries_relation')->references('id')->on('static_html_entries');
            $table->foreign('template_id', 'template_id_relation')->references('id')->on('static_html_templates');
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
		Schema::drop('entry_to_template_mappings');
	}

}
