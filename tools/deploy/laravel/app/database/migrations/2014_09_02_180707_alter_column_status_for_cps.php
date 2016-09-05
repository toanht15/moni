<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterColumnStatusForCps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cps', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('show_lp_flg');

        });

        Schema::table('cps', function (Blueprint $table) {

            $table->tinyInteger('status')->default(1)->after('fix_attract_flg');
            $table->tinyInteger('show_lp_flg')->default(1)->after('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::table('cps', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('show_lp_flg');

        });

        Schema::table('cps', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('fix_attract_flg');
            $table->tinyInteger('show_lp_flg')->default(0)->after('status');

        });
    }

}
