<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCpPhotoActions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE cp_photo_actions MODIFY COLUMN text LONGTEXT DEFAULT "" NOT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        DB::statement('ALTER TABLE cp_photo_actions MODIFY COLUMN text VARCHAR(255) DEFAULT "" NOT NULL');
	}

}
