<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCloseFlgToCpsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cps', function(Blueprint $table)
        {
            $table->dateTime('cp_page_close_date')->default('0000-00-00 00:00:00')->after('public_date');
            $table->tinyInteger('use_cp_page_close_flg')->default(0)->after('archive_flg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cps', function(Blueprint $table)
        {
            $table->dropColumn('cp_page_close_date');
            $table->dropColumn('use_cp_page_close_flg');
        });
    }

}
