<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertActionTitle extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $table = array('cp_announce_actions', 'cp_buttons_actions', 'cp_entry_actions', 'cp_fangate_actions', 'cp_free_answer_actions', 'cp_message_actions', 'cp_questionnaire_actions', 'cp_shipping_address_actions');
        foreach ($table as $name){
            Schema::table($name, function(Blueprint $t)
            {
                $t->string('title', 255)->default('')->after('id');
            });
        }

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        $table = array('cp_announce_actions', 'cp_buttons_actions', 'cp_entry_actions', 'cp_fangate_actions', 'cp_free_answer_actions', 'cp_message_actions', 'cp_questionnaire_actions', 'cp_shipping_address_actions');
        foreach ($table as $name) {
            Schema::table($name, function (Blueprint $t) {
                $t->dropColumn('title');
            });
        }
	}

}
