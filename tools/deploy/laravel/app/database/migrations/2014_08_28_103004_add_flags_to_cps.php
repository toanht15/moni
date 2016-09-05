<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFlagsToCps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cps', function (Blueprint $table) {

            $table->dropColumn('close_flg');
            $table->dropColumn('public_flg');

            $table->tinyInteger('show_lp_flg')->default(0)->after('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::table('cps', function (Blueprint $table) {

            $table->dropColumn('show_lp_flg');
            $table->tinyInteger('close_flg')->default(0)->after('status');
            $table->tinyInteger('public_flg')->default(0)->after('close_flg');

        });
    }
}
