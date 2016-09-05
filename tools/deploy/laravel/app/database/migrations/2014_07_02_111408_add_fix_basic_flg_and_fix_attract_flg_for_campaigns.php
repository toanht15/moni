<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFixBasicFlgAndFixAttractFlgForCampaigns extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::table('campaigns', function (Blueprint $table) {
            $table->tinyInteger('fix_basic_flg')->default(0)->after("get_address_type");
            $table->tinyInteger('fix_attract_flg')->default(0)->after("fix_basic_flg");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn("fix_basic_flg");
            $table->dropColumn("fix_attract_flg");
        });
    }

}
