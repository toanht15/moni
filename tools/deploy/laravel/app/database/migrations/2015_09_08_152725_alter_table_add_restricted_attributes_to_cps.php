<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddRestrictedAttributesToCps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cps', function (Blueprint $table) {
            $table->tinyInteger('restricted_age_flg')->default(0)->after('archive_flg');
            $table->tinyInteger('restricted_age')->default(0)->unsigned()->after('restricted_age_flg');
            $table->tinyInteger('restricted_gender_flg')->default(0)->after('restricted_age');
            $table->tinyInteger('restricted_gender')->default(0)->unsigned()->after('restricted_gender_flg');
            $table->tinyInteger('restricted_address_flg')->default(0)->after('restricted_gender');
        });

        Schema::create('cp_restricted_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cp_id');
            $table->unsignedInteger('pref_id');
            $table->tinyInteger('del_flg')->default(0);
            $table->timestamps();

            $table->foreign('cp_id')->references('id')->on('cps');
            $table->foreign('pref_id')->references('id')->on('prefectures');
            $table->index('cp_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cps', function(Blueprint $table) {
            $table->dropColumn('restricted_age_flg');
            $table->dropColumn('restricted_age');
            $table->dropColumn('restricted_gender_flg');
            $table->dropColumn('restricted_gender');
            $table->dropColumn('restricted_address_flg');
        });

        Schema::drop('cp_restricted_addresses');
    }

}
