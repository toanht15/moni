<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndTypeEndAtToCpActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cp_actions', function(Blueprint $table)
        {
            $table->tinyInteger('end_type')->default(-1)->after('status');
            $table->datetime('end_at')->default('0000-00-00 00:00:00')->after('end_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_actions', function(Blueprint $table)
        {
            $t->dropColumn('end_type');
            $t->dropColumn('end_at');
        });
    }
}
