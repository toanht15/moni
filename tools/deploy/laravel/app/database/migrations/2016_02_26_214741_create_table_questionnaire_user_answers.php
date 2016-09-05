<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuestionnaireUserAnswers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('questionnaire_user_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cp_action_id');
            $table->unsignedBigInteger('brands_users_relation_id');
            $table->tinyInteger('approval_status')->default(0);
            $table->boolean('del_flg')->default(0);
            $table->timestamps();
            $table->foreign('cp_action_id')->references('id')->on('cp_actions');
            $table->foreign('brands_users_relation_id')->references('id')->on('brands_users_relations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('questionnaire_user_answers');
    }

}
