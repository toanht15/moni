<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSnsAcitonCountLogsRenameSnsUid extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('sns_action_count_logs', function(Blueprint $table) {
            $table->renameColumn('sns_uid', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('sns_action_count_logs', function(Blueprint $table) {
            $table->renameColumn('user_id', 'sns_uid');
        });
    }

}
