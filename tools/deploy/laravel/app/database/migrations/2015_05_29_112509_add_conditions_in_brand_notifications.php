<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConditionsInBrandNotifications extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::table('brand_notifications', function(Blueprint $t)
        {
            $t->tinyInteger('conditions')->after('publish_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brand_notifications', function(Blueprint $t)
        {
            $t->dropColumn('conditions');
        });
    }

}
