<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRateToBrandsUsersRelations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands_users_relations', function(Blueprint $table)
        {
            $table->tinyInteger('rate')->default(0)->after('score');
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
            $table->dropColumn('rate');
        });
    }

}
