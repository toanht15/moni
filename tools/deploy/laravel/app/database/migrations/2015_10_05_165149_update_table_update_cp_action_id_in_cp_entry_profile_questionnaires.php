<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableUpdateCpActionIdInCpEntryProfileQuestionnaires extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::statement('UPDATE cp_entry_profile_questionnaires cep, cp_entry_actions cea SET cep.cp_action_id = cea.cp_action_id WHERE cep.cp_entry_action_id = cea.id');

        DB::statement('UPDATE cp_actions ca, cp_entry_actions cea SET ca.prefill_flg = cea.prefill_flg WHERE cea.cp_action_id = ca.id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement('UPDATE cp_entry_profile_questionnaires cep SET cep.cp_action_id = NULL');

        DB::statement('UPDATE cp_actions ca SET ca.prefill_flg = 1');
    }

}
