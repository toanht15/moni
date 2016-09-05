<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHtmlContentToCpFreeAnswerActions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cp_free_answer_actions', function (Blueprint $table) {
            $table->text("html_content")->after("question");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cp_free_answer_actions', function (Blueprint $table) {
            $table->dropColumn("html_content");
        });
    }

}
