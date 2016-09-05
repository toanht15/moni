<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStaticHtmlEntriesMetaTitle extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement('ALTER TABLE `static_html_entries` MODIFY `meta_title` varchar(64) after `extra_body`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement('ALTER TABLE `static_html_entries` MODIFY `meta_title` varchar(64) after `extra_body`');
    }

}
