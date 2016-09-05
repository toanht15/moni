<?php

use Illuminate\Database\Migrations\Migration;

class ModifyEntriesColumn extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        DB::statement('ALTER TABLE `facebook_entries` MODIFY  `image_url` VARCHAR(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `twitter_entries` MODIFY  `image_url` VARCHAR(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `youtube_entries` MODIFY  `image_url` VARCHAR(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `link_entries` MODIFY  `image_url` VARCHAR(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `rss_entries` MODIFY  `image_url` VARCHAR(512) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        DB::statement('ALTER TABLE `facebook_entries` MODIFY  `image_url` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `twitter_entries` MODIFY  `image_url` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `youtube_entries` MODIFY  `image_url` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `link_entries` MODIFY  `image_url` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');
        DB::statement('ALTER TABLE `rss_entries` MODIFY  `image_url` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ""');

    }

}
