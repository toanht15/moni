<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSnsPluginToCategory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('static_html_categories', function(Blueprint $table)
        {
            $table->text('sns_plugin_tag_text')->after('og_image_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('static_html_categories', function(Blueprint $table)
        {
            $table->dropColumn('sns_plugin_tag_text');
        });
    }

}
