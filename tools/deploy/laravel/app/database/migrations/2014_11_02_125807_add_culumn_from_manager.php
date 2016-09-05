<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCulumnFromManager extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operation_log_admin_data', function(Blueprint $table)
        {
            $table->boolean('from_manager')->default(0)->after('referer_url');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operation_log_admin_data', function(Blueprint $table)
        {
            $table->dropColumn('from_manager');
        });
    }

}
