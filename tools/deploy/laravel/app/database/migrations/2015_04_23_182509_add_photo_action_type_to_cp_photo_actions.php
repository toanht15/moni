<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhotoActionTypeToCpPhotoActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('cp_photo_actions', function(Blueprint $table) {
            $table->tinyInteger('title_required')->default(0)->after('cp_action_id');
            $table->tinyInteger('comment_required')->default(0)->after('title_required');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('cp_photo_actions', function(Blueprint $table) {
            $table->dropColumn('title_required');
            $table->dropColumn('comment_required');
        });
	}

}
