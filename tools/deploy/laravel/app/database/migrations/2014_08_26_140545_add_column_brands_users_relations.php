<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnBrandsUsersRelations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands_users_relations', function(Blueprint $table)
        {
            $table->dateTime('last_login_date')->default('0000-00-00 00:00:00')->after('from_kind');
            $table->integer('login_count')->unsigned()->default(0)->after('last_login_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands_users_relations', function(Blueprint $table)
        {
            $table->dropColumn('last_login_date');
            $table->dropColumn('login_count');
        });
    }

}
