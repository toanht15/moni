<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActionIdMessageAlertChecks extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_alert_checks', function(Blueprint $table)
        {
            $table->integer('cp_id')->unsigned()->default(0)->after('cp_message_delivery_reservation_id');
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
            $table->dropColumn('cp_id');
        });
    }

}
