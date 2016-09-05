<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPageEntriesManualOffFlg extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('page_entries', function (Blueprint $table) {
            $table->boolean('manual_off_flg')->default(0)->after('pub_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('page_entries', function (Blueprint $table) {
            $table->dropColumn('manual_off_flg');
        });
    }
}
