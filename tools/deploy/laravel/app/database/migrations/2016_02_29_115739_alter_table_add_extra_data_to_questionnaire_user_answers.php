<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAddExtraDataToQuestionnaireUserAnswers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('questionnaire_user_answers', function (Blueprint $table) {
            $table->unsignedInteger('finished_answer_id')->after('brands_users_relation_id');
            $table->dateTime('finished_answer_at')->after('approval_status');
            $table->unique('finished_answer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('questionnaire_user_answers', function (Blueprint $table) {
            $table->dropColumn('finished_answer_id');
            $table->dropColumn('finished_answer_at');
        });
    }

}
