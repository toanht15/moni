<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableConstraintQuestionnaireUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questionnaire_users', function(Blueprint $t)
        {
            $t->dropForeign('questionnaire_users_user_id_foreign');
            $t->dropColumn('user_id');
            $t->integer('cp_user_id')->unsigned()->after('id');
            $t->index('cp_user_id');
            $t->foreign('cp_user_id')->references('id')->on('cp_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questionnaire_users', function(Blueprint $t)
        {
            $t->dropForeign('questionnaire_users_cp_user_id_foreign');
            $t->dropColumn('cp_user_id');
            $t->bigInteger('user_id')->unsigned()->after('id');
            $t->index('user_id');
            $t->foreign('user_id')->references('id')->on('users');
        });
    }

}
