<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddPermanentFlgToCps extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cps', function(Blueprint $table) {
            $table->tinyInteger('permanent_flg')->after('reference_url')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cps', function(Blueprint $table) {
            $table->dropColumn('permanent_flg');
        });
    }

}
