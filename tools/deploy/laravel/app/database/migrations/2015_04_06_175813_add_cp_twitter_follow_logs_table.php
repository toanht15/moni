<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCpTwitterFollowLogsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cp_twitter_follow_logs', function(Blueprint $table)
        {
            $table->tinyInteger('status')->default(0)->after('action_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_twitter_follow_logs', function(Blueprint $table)
        {
            $table->dropColumn('status');
        });
    }
}
