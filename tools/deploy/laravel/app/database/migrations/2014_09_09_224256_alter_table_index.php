<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableIndex extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_social_accounts', function(Blueprint $table)
        {
            $table->dropIndex('brand_social_accounts_social_app_id_index');
        });

        Schema::table('cp_next_action_info', function(Blueprint $table)
        {
            $table->dropForeign('cp_next_action_info_next_action_table_id_foreign');
            $table->dropUnique('cp_next_action_info_next_action_table_id_unique');
            $table->foreign('next_action_table_id')->references('id')->on('cp_next_actions');
        });

        Schema::table('cp_announce_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_announce_actions_cp_action_id_foreign');
            $table->dropUnique('cp_announce_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::table('cp_buttons_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_buttons_actions_cp_action_id_foreign');
            $table->dropUnique('cp_buttons_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::table('cp_engagement_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_fangate_actions_cp_action_id_foreign');
            $table->dropUnique('cp_fangate_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::table('cp_entry_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_entry_actions_cp_action_id_foreign');
            $table->dropUnique('cp_entry_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::table('cp_free_answer_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_free_answer_actions_cp_action_id_foreign');
            $table->dropUnique('cp_free_answer_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::table('cp_message_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_message_actions_cp_action_id_foreign');
            $table->dropUnique('cp_message_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::table('cp_questionnaire_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_questionnaire_actions_cp_action_id_foreign');
            $table->dropUnique('cp_questionnaire_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });

        Schema::table('cp_shipping_address_actions', function(Blueprint $table)
        {
            $table->dropForeign('cp_shipping_address_actions_cp_action_id_foreign');
            $table->dropUnique('cp_shipping_address_actions_cp_action_id_unique');
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brand_social_accounts', function(Blueprint $table)
        {
            $table->index(array('social_app_id'), 'brand_social_accounts_social_app_id_index');
        });

        Schema::table('cp_next_action_info', function(Blueprint $table)
        {
            $table->unique(array('next_action_table_id'), 'cp_next_action_info_next_action_table_id_unique');
        });

        Schema::table('cp_announce_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_announce_actions_cp_action_id_unique');
        });

        Schema::table('cp_buttons_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_buttons_actions_cp_action_id_unique');
        });

        Schema::table('cp_engagement_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_fangate_actions_cp_action_id_unique');
        });

        Schema::table('cp_entry_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_entry_actions_cp_action_id_unique');
        });

        Schema::table('cp_free_answer_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_free_answer_actions_cp_action_id_unique');
        });

        Schema::table('cp_message_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_message_actions_cp_action_id_unique');
        });

        Schema::table('cp_questionnaire_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_questionnaire_actions_cp_action_id_unique');
        });

        Schema::table('cp_shipping_address_actions', function(Blueprint $table)
        {
            $table->unique(array('cp_action_id'), 'cp_shipping_address_actions_cp_action_id_unique');
        });
    }

}
