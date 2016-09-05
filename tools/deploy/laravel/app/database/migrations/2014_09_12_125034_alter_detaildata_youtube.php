<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDetaildataYoutube extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('youtube_entries', function(Blueprint $table)
        {
            $table->renameColumn('detail_data', 'extra_data');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('youtube_entries', function(Blueprint $table)
        {
            $table->renameColumn('extra_data', 'detail_data');
        });
	}

}
