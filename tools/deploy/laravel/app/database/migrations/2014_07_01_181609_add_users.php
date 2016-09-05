<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $t)
        {
            $t->dateTime('last_login_date')->default('0000-00-00 00:00:00')->after('mp_token_update_at');
            $t->integer('login_count')->unsigned()->default(0)->after('last_login_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $t)
        {
            $t->dropColumn('last_login_date');
            $t->dropColumn('login_count');
        });
    }

}
