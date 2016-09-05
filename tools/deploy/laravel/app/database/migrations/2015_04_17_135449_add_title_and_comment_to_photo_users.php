<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleAndCommentToPhotoUsers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('photo_users', function(Blueprint $table) {
            $table->string('photo_title', 50)->default('')->after('photo_url');
            $table->string('photo_comment', 300)->default('')->after('photo_title');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('photo_users', function(Blueprint $table) {
            $table->dropColumn('photo_title');
            $table->dropColumn('photo_comment');
        });
	}

}
