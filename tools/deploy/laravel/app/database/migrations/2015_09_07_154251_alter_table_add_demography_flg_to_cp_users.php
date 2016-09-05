<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddDemographyFlgToCpUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_users', function(Blueprint $table) {
            $table->tinyInteger('demography_flg')->default(1)->after('beginner_flg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_users', function(Blueprint $table) {
            $table->dropColumn('demography_flg');
        });
    }

}
