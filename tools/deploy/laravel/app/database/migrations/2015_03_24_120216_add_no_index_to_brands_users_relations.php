<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoIndexToBrandsUsersRelations extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands_users_relations', function(Blueprint $table)
        {
            $table->index('no');
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
            $table->dropIndex('brands_users_relations_no_index');
        });
    }

}
