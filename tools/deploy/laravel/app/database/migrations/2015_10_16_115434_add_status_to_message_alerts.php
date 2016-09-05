<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToMessageAlerts extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_alert_checks', function(Blueprint $table)
        {
            $table->tinyInteger('checked')->after('cp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_alert_checks', function(Blueprint $table)
        {
            $table->dropColumn('checked');
        });
    }

}
