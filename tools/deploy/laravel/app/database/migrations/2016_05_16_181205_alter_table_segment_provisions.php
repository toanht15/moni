<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSegmentProvisions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('segment_provisions', function(Blueprint $table) {
            $table->renameColumn('unclassified_flg', 'type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('segment_provisions', function(Blueprint $table) {
            $table->renameColumn('type', 'unclassified_flg');
        });
    }

}
