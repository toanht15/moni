<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPanelCommentToInstagramEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE `instagram_entries` MODIFY `panel_text` VARCHAR(300) NOT NULL');
        Schema::table('instagram_entries', function(Blueprint $table) {
            $table->string('panel_comment', 300)->default('')->after('panel_text');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE `instagram_entries` MODIFY `panel_text` VARCHAR(255)');
        Schema::table('instagram_entries', function(Blueprint $table) {
            $table->dropColumn('panel_comment');
        });
	}

}
