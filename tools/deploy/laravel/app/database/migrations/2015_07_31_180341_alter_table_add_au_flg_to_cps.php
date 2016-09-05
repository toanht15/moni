<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddAuFlgToCps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cps', function(Blueprint $table) {
            $table->tinyInteger('au_flg')->default(0)->after('archive_flg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cps', function(Blueprint $table) {
            $table->dropColumn('au_flg');
        });
    }

}
