<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropStaticHtmlCategoriesNameIndex extends Migration {

	public function up()
	{
		Schema::table('static_html_categories', function(Blueprint $table)
		{
			$table->dropIndex("static_html_categories_name_index");
		});
	}

	public function down()
	{
		Schema::table('static_html_categories', function(Blueprint $table)
		{
			$table->index("name");
		});
	}

}
