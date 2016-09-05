<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableStaticHtmlEmbedEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('static_html_embed_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('static_html_entry_id');
            $table->tinyInteger('public_flg')->default(1);
            $table->timestamps();
            $table->foreign('static_html_entry_id')->references('id')->on('static_html_entries');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('static_html_embed_entries');
	}

}
