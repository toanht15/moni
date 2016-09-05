<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddCpActionIdToCpEntryProfileQuestionnaires extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_entry_profile_questionnaires', function(Blueprint $table) {
            $table->unsignedInteger('cp_action_id')->after('cp_entry_action_id')->nullable();
        });

        Schema::table('cp_actions', function(Blueprint $table) {
            $table->tinyInteger('prefill_flg')->after('end_at')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_entry_profile_questionnaires', function(Blueprint $table) {
            $table->dropColumn('cp_action_id');
        });

        Schema::table('cp_actions', function(Blueprint $table) {
            $table->dropColumn('prefill_flg');
        });
    }

}
