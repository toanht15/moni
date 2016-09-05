<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCvTagToCpJoinFinishActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cp_join_finish_actions', function(Blueprint $table)
        {
            $table->text('cv_tag')->after('text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_join_finish_actions', function(Blueprint $table)
        {
            $table->dropColumn('cv_tag');
        });
    }

}
