<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyEntriesTextColumn extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE `facebook_entries` MODIFY  `panel_text` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `twitter_entries` MODIFY  `panel_text` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `youtube_entries` MODIFY  `panel_text` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `rss_entries` MODIFY  `panel_text` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE `facebook_entries` MODIFY  `panel_text` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `twitter_entries` MODIFY  `panel_text` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `youtube_entries` MODIFY  `panel_text` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `rss_entries` MODIFY  `panel_text` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
	}

}
