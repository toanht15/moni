<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticHtmlEntryCategories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('static_html_entry_categories', function(Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedInteger('static_html_entry_id')->default(0);
            $table->unsignedInteger('category_id')->default(0);
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('static_html_categories');
            $table->foreign('static_html_entry_id')->references('id')->on('static_html_entries');

            $table->index('static_html_entry_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('static_html_entry_categories');
    }

}
