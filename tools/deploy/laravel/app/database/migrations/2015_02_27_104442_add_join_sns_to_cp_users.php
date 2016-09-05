<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddJoinSnsToCpUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cp_users', function(Blueprint $table)
        {
            $table->tinyInteger("join_sns")->default(0)->after("beginner_flg");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_users', function(Blueprint $table)
        {
            $table->dropColumn("join_sns");
        });
    }

}
