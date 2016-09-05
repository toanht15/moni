<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPanelTypeToSnsEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('facebook_entries', function(Blueprint $table) {
            $table->tinyInteger("target_type")->default(1)->after("link");
        });

        Schema::table('twitter_entries', function(Blueprint $table) {
            $table->tinyInteger("target_type")->default(1)->after("link");
        });

        Schema::table('youtube_entries', function(Blueprint $table) {
            $table->tinyInteger("target_type")->default(1)->after("link");
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('facebook_entries', function(Blueprint $table) {
            $table->dropColumn("target_type");
        });

        Schema::table('twitter_entries', function(Blueprint $table) {
            $table->dropColumn("target_type");
        });

        Schema::table('youtube_entries', function(Blueprint $table) {
            $table->dropColumn("target_type");
        });
	}

}
