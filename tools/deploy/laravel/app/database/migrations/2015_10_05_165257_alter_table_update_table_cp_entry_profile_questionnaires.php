<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUpdateTableCpEntryProfileQuestionnaires extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement('RENAME TABLE cp_entry_profile_questionnaires TO cp_profile_questionnaires');

        DB::statement('ALTER TABLE cp_profile_questionnaires MODIFY COLUMN cp_action_id INTEGER UNSIGNED NOT NULL');

        Schema::table('cp_profile_questionnaires', function(Blueprint $table) {
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');

            $table->dropForeign('cp_entry_profile_questionnaires_cp_entry_action_id_foreign');

            $table->dropColumn('cp_entry_action_id');
        });

        Schema::table('cp_entry_actions', function(Blueprint $table) {
            $table->dropColumn('prefill_flg');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement('RENAME TABLE cp_profile_questionnaires TO cp_entry_profile_questionnaires');

        DB::statement('ALTER TABLE cp_entry_profile_questionnaires MODIFY COLUMN cp_action_id INTEGER UNSIGNED NULL');

        Schema::table('cp_entry_profile_questionnaires', function(Blueprint $table) {
            $table->unsignedInteger('cp_entry_action_id')->after('profile_questionnaire_id')->nullable();
        });

        DB::statement('UPDATE cp_entry_profile_questionnaires cep, cp_entry_actions cea SET cep.cp_entry_action_id = cea.id WHERE cep.cp_action_id = cea.cp_action_id');

        DB::statement('ALTER TABLE cp_entry_profile_questionnaires MODIFY COLUMN cp_entry_action_id INTEGER UNSIGNED NOT NULL');

        Schema::table('cp_entry_profile_questionnaires', function(Blueprint $table) {
            $table->foreign('cp_entry_action_id')->references('id')->on('cp_entry_actions');

            $table->dropForeign('cp_profile_questionnaires_cp_action_id_foreign');
        });

        Schema::table('cp_entry_actions', function(Blueprint $table) {
            $table->tinyInteger('prefill_flg')->after('button_label_text')->default(1);
        });

        DB::statement('UPDATE cp_entry_actions cea, cp_actions ca SET cea.prefill_flg = ca.prefill_flg WHERE cea.cp_action_id = ca.id');
    }

}
