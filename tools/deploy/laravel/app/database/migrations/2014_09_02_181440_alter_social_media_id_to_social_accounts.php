<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSocialMediaIdToSocialAccounts extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        DB::statement('ALTER TABLE `social_accounts` MODIFY `social_media_id` tinyint(4) NOT NULL');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        DB::statement('ALTER TABLE `social_accounts` MODIFY `social_media_id` int(10) unsigned NOT NULL');

    }

}
