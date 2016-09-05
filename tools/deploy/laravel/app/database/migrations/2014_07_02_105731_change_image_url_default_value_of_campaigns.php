<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImageUrlDefaultValueOfCampaigns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('campaigns', function(Blueprint $table)
		{
            $table->dropColumn('image_url');
		});


        Schema::table('campaigns', function(Blueprint $table)
        {
            $table->string('image_url')->default("")->after("winner_count");
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

        Schema::table('campaigns', function(Blueprint $table)
        {
            $table->dropColumn('image_url');
        });

		Schema::table('campaigns', function(Blueprint $table)
		{
            $table->string('image_url')->default(0)->after("winner_count");
		});
	}

}
